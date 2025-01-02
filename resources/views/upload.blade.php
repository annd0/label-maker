<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Tag Generator</h2>

        <!-- Display Success Message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Display Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- File Upload Form -->
        <form id="uploadForm" action="/generate-labels" method="POST" enctype="multipart/form-data" class="m-5">
            @csrf
            <div class="mb-3">
                <label for="file" class="form-label">Choose a File</label>
                <input type="file" class="form-control" name="file" required>
            </div>
            <div class="mb-3">
                <label for="product">Filter by Product:</label>
                <select class="form-select" name="product" id="product" required>
                    <option value="Bedloft">Bedloft</option>
                    <option value="Combination Unit">Combination Unit</option>
                    <option value="Futon">Futon</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>
    <div class="container mt-5">
        <div class="m-5">
            @if (!empty($files))
                <h3>Last 10 Generated Files</h3>
                <ul>
                    @foreach ($files as $file)
                        <li>
                            <a href="{{ route('download.file', ['file' => basename($file)]) }}">
                                {{ basename($file) }} ({{ date('Y-m-d H:i:s', Storage::lastModified($file)) }})
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>No files have been generated yet.</p>
            @endif
        </div>
    </div>
</body>
</html>

