@extends('admin.components.admin')

@section('title', 'Upload Theme')
@section('header', 'Upload Theme')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div x-data="{ uploading: false, progress: 0, message: '', error: false }">
                    <form id="uploadForm" @submit.prevent="uploadTheme" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-6">
                            <label for="theme_zip" class="block text-sm font-medium text-gray-700 mb-2">Theme Zip
                                File</label>
                            <input type="file" name="theme_zip" id="theme_zip" accept=".zip" required
                                @change="error = false; message = ''" class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-indigo-50 file:text-indigo-700
                                        hover:file:bg-indigo-100">
                            <p class="mt-2 text-sm text-gray-500">Upload a valid theme .zip file.</p>
                        </div>

                        <div class="mb-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="activate" name="activate" type="checkbox" value="true"
                                        class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="activate" class="font-medium text-gray-700">Activate Theme</label>
                                    <p class="text-gray-500">Automatically activate this theme after upload.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div x-show="uploading" class="mb-6" style="display: none;">
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-indigo-700" x-text="message">Uploading...</span>
                                <span class="text-sm font-medium text-indigo-700" x-text="progress + '%'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300"
                                    :style="'width: ' + progress + '%'"></div>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div x-show="message && !uploading" class="mb-6 p-4 rounded-md"
                            :class="error ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'" style="display: none;">
                            <p x-text="message"></p>
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('admin.themes.index') }}" class="text-gray-500 hover:text-gray-700 mr-4"
                                :class="{ 'pointer-events-none opacity-50': uploading }">Cancel</a>
                            <button type="submit" :disabled="uploading"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <span x-show="!uploading">Upload Theme</span>
                                <span x-show="uploading">Processing...</span>
                            </button>
                        </div>
                    </form>

                    <script>
                        function uploadTheme() {
                            const form = document.getElementById('uploadForm');
                            const fileInput = document.getElementById('theme_zip');

                            
                            const file = fileInput.files[0];
                            const maxSize = 64 * 1024 * 1024; // 64MB

                            if (file.size > maxSize) {
                                this.error = true;
                                this.message = 'File is too large using. Maximum allowed size is 64MB.';
                                return;
                            }

                            this.uploading = true;
                            this.progress = 0;
                            this.message = 'Uploading...';
                            this.error = false;

                            const formData = new FormData(form);
                            const xhr = new XMLHttpRequest();

                            xhr.upload.addEventListener('progress', (e) => {
                                if (e.lengthComputable) {
                                    this.progress = Math.round((e.loaded / e.total) * 100);
                                    if (this.progress === 100) {
                                        this.message = 'Extracting... (this may take a moment)';
                                    }
                                }
                            });

                            xhr.addEventListener('load', () => {
                                if (xhr.status >= 200 && xhr.status < 300) {
                                    this.message = 'Theme uploaded and extracted successfully!';
                                    this.progress = 100;
                                    setTimeout(() => {
                                        window.location.href = "{{ route('admin.themes.index') }}";
                                    }, 1000);
                                } else {
                                    this.uploading = false;
                                    this.error = true;
                                    try {
                                        const response = JSON.parse(xhr.responseText);
                                        this.message = response.message || 'Upload failed.';
                                    } catch (e) {
                                        this.message = 'Upload failed with status: ' + xhr.status;
                                    }
                                }
                            });

                            xhr.addEventListener('error', () => {
                                this.uploading = false;
                                this.error = true;
                                this.message = 'Network error occurred.';
                            });

                            xhr.open('POST', "{{ route('admin.themes.upload') }}");
                            xhr.send(formData);
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
@endsection