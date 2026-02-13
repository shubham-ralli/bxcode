<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = Media::latest();

        // Type Filter e.g. ?type=image
        if ($request->has('type') && $request->type != 'all') {
            $type = $request->type;
            if ($type == 'image') {
                $query->where('mime_type', 'like', 'image/%');
            } elseif ($type == 'audio') {
                $query->where('mime_type', 'like', 'audio/%');
            } elseif ($type == 'video') {
                $query->where('mime_type', 'like', 'video/%');
            } elseif ($type == 'document') {
                $query->where(function ($q) {
                    $q->where('mime_type', 'application/pdf')
                        ->orWhere('mime_type', 'like', 'application/msword')
                        ->orWhere('mime_type', 'like', 'application/vnd.openxmlformats-officedocument.%')
                        ->orWhere('mime_type', 'text/plain');
                });
            }
        }

        // Date Filter e.g. ?date=2023-10
        if ($request->has('date') && $request->date != 'all') {
            $date = $request->date; // Y-m
            $parts = explode('-', $date);
            if (count($parts) == 2) {
                $query->whereYear('created_at', $parts[0])
                    ->whereMonth('created_at', $parts[1]);
            }
        }

        // Search Filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('filename', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        $media = $query->paginate(40);

        // Get all unique dates for filter dropdown
        $dates = Media::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as date, DATE_FORMAT(created_at, "%M %Y") as label')
            ->distinct()
            ->orderBy('date', 'desc')
            ->get();

        if ($request->ajax()) {
            $items = collect($media->items())->map(function ($item) {
                $item->full_url = asset(ltrim($item->path, '/'));
                return $item;
            });

            return response()->json([
                'html' => view('admin.media.partials.media-items', compact('media'))->render(),
                'items' => $items,
                'next_page_url' => $media->nextPageUrl(),
                'total' => $media->total(),
                'first_item' => $media->firstItem(),
                'last_item' => $media->lastItem(),
            ]);
        }

        return view('admin.media.index', compact('media', 'dates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,mp3,wav,ogg,mp4,webm,pdf,doc,docx,xls,xlsx,txt,zip|max:40960', // 40MB
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $year = date('Y');
            $month = date('m');
            $directory = "{$year}/{$month}";

            // Generate filename with counter if exists
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug($originalName) . '.' . $extension;

            $counter = 1;
            while (Storage::disk('uploads')->exists("{$directory}/{$filename}")) {
                $filename = Str::slug($originalName) . '-' . $counter . '.' . $extension;
                $counter++;
            }

            $path = $file->storeAs($directory, $filename, 'uploads');

            $media = Media::create([
                'filename' => $filename,
                'path' => '/uploads/' . $directory . '/' . $filename,
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'title' => $originalName,
            ]);

            if ($request->expectsJson()) {
                // If uploading from Media Library Grid, return the HTML for the new item
                if ($request->has('return_html')) {
                    $html = view('admin.media.partials.media-items', ['media' => [$media]])->render();
                    return response()->json(['success' => true, 'html' => $html]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Image uploaded successfully',
                    'data' => $media,
                    'location' => asset($media->path), // For TinyMCE compatibility if widely used
                    'id' => $media->id
                ]);
            }

            return back()->with('success', 'Image uploaded successfully');
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'No image selected'], 422);
        }

        return back()->with('error', 'No image selected');
    }

    public function update(Request $request, $id)
    {
        $media = Media::findOrFail($id);

        $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'caption' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $media->update([
            'alt_text' => $request->alt_text,
            'title' => $request->title,
            'caption' => $request->caption,
            'description' => $request->description,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Saved']);
        }

        return back()->with('success', 'Media updated successfully');
    }

    /**
     * Handle TinyMCE Image Upload
     */
    public function tinyMceUpload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $year = date('Y');
            $month = date('m');
            $directory = "{$year}/{$month}";
            $filename = uniqid() . '_' . $file->getClientOriginalName();

            // Check if file exists
            while (Storage::disk('uploads')->exists("{$directory}/{$filename}")) {
                $filename = uniqid() . '_' . $file->getClientOriginalName();
            }

            // Store file
            $path = $file->storeAs($directory, $filename, 'uploads');

            if ($path) {
                return response()->json([
                    'location' => '/uploads/' . $directory . '/' . $filename
                ]);
            }
        }

        return response()->json(['error' => 'Upload failed'], 500);
    }

    public function destroy(Media $media)
    {
        // Remove prefixes to get relative path for 'uploads' disk
        $relativePath = str_replace(['/uploads/', 'uploads/'], '', $media->path);

        if (Storage::disk('uploads')->exists($relativePath)) {
            Storage::disk('uploads')->delete($relativePath);
        }

        $media->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Media deleted successfully');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:media,id',
        ]);

        $ids = $request->ids;
        $mediaItems = Media::whereIn('id', $ids)->get();

        foreach ($mediaItems as $media) {
            // Remove prefixes to get relative path for 'uploads' disk
            $relativePath = str_replace(['/uploads/', 'uploads/'], '', $media->path);

            if (Storage::disk('uploads')->exists($relativePath)) {
                Storage::disk('uploads')->delete($relativePath);
            }
            $media->delete();
        }

        return response()->json(['success' => true, 'message' => 'Selected items deleted successfully']);
    }
}
