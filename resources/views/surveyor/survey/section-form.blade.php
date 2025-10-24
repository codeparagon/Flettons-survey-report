@extends('layouts.app')

@section('title', $section->display_name . ' Assessment - ' . $survey->client_name)

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="page-header">
            <h2 class="pageheader-title">{{ $section->display_name }} Assessment</h2>
            <div class="page-breadcrumb">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.surveys.index') }}">Surveys</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.surveys.show', $survey) }}">Survey #{{ $survey->id }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('surveyor.survey.sections', $survey) }}">Sections</a></li>
                        <li class="breadcrumb-item active">{{ $section->display_name }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $section->display_name }} Assessment Form</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('surveyor.survey.section.save', [$survey, $section]) }}" 
                      method="POST" 
                      enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group">
                        <label for="condition_rating">Overall Condition Rating <span class="text-danger">*</span></label>
                        <select class="form-control" id="condition_rating" name="condition_rating" required>
                            <option value="">Select Condition</option>
                            <option value="excellent" {{ old('condition_rating', $assessment->condition_rating) == 'excellent' ? 'selected' : '' }}>
                                Excellent - No issues found
                            </option>
                            <option value="good" {{ old('condition_rating', $assessment->condition_rating) == 'good' ? 'selected' : '' }}>
                                Good - Minor maintenance needed
                            </option>
                            <option value="fair" {{ old('condition_rating', $assessment->condition_rating) == 'fair' ? 'selected' : '' }}>
                                Fair - Some repairs required
                            </option>
                            <option value="poor" {{ old('condition_rating', $assessment->condition_rating) == 'poor' ? 'selected' : '' }}>
                                Poor - Major repairs needed
                            </option>
                        </select>
                        @error('condition_rating')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="defects_noted">Defects Noted</label>
                        <textarea class="form-control" 
                                  id="defects_noted" 
                                  name="defects_noted" 
                                  rows="4" 
                                  placeholder="Describe any defects, damage, or issues found...">{{ old('defects_noted', $assessment->defects_noted) }}</textarea>
                        @error('defects_noted')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="recommendations">Recommendations</label>
                        <textarea class="form-control" 
                                  id="recommendations" 
                                  name="recommendations" 
                                  rows="4" 
                                  placeholder="Provide recommendations for repairs or maintenance...">{{ old('recommendations', $assessment->recommendations) }}</textarea>
                        @error('recommendations')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="notes">Additional Notes</label>
                        <textarea class="form-control" 
                                  id="notes" 
                                  name="notes" 
                                  rows="3" 
                                  placeholder="Any additional observations or comments...">{{ old('notes', $assessment->notes) }}</textarea>
                        @error('notes')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="photos">Photos</label>
                        <input type="file" 
                               class="form-control-file" 
                               id="photos" 
                               name="photos[]" 
                               multiple 
                               accept="image/*">
                        <small class="form-text text-muted">
                            Upload multiple photos to document the condition. Max 5MB per photo.
                        </small>
                        @error('photos.*')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($assessment->photos && count($assessment->photos) > 0)
                    <div class="form-group">
                        <label>Existing Photos</label>
                        <div class="row">
                            @foreach($assessment->photos as $photo)
                            <div class="col-md-3 mb-2">
                                <div class="position-relative">
                                    <img src="{{ Storage::url($photo) }}" 
                                         class="img-thumbnail" 
                                         style="width: 100%; height: 100px; object-fit: cover;">
                                    <button type="button" 
                                            class="btn btn-sm btn-danger position-absolute" 
                                            style="top: 5px; right: 5px;"
                                            onclick="deletePhoto('{{ $photo }}')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Assessment
                        </button>
                        <a href="{{ route('surveyor.survey.sections', $survey) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Sections
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Survey Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <th width="100">Client:</th>
                        <td>{{ $survey->client_name }}</td>
                    </tr>
                    <tr>
                        <th>Property:</th>
                        <td>{{ $survey->property_address_full }}</td>
                    </tr>
                    <tr>
                        <th>Level:</th>
                        <td><span class="badge badge-info">{{ $survey->level }}</span></td>
                    </tr>
                    <tr>
                        <th>Section:</th>
                        <td>{{ $section->display_name }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Section Description</h5>
            </div>
            <div class="card-body">
                <p class="card-text">{{ $section->description }}</p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function deletePhoto(photoPath) {
    if (confirm('Are you sure you want to delete this photo?')) {
        fetch('{{ route("surveyor.survey.section.deletePhoto", [$survey, $section]) }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                photo_path: photoPath
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting photo');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting photo');
        });
    }
}
</script>
@endsection

