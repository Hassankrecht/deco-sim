@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h2>{{ __('admin.edit_food') }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.foods.update', $food->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div class="form-group mb-3">
            <label for="name">{{ __('admin.food_name') }}</label>
            <input type="text" name="name" value="{{ old('name', $food->name) }}" class="form-control" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <!-- Description -->
        <div class="form-group mb-3">
            <label for="description">{{ __('admin.description') }}</label>
            <textarea name="description" class="form-control" rows="4" required>{{ old('description', $food->description) }}</textarea>
            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <!-- Category -->
        <div class="form-group mb-3">
            <label for="category_id">{{ __('admin.category') }}</label>
            <select name="category_id" class="form-control" required>
                <option value="">{{ __('admin.select_category') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $food->category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <!-- Price -->
        <div class="form-group mb-3">
            <label for="price">{{ __('admin.price') }}</label>
            <input type="number" name="price" value="{{ old('price', $food->price) }}" class="form-control" required step="0.01">
            @error('price') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <!-- Image -->
        <div class="form-group mb-3">
            <label for="image">{{ __('admin.image') }}</label>
            <input type="file" name="image" class="form-control">
            @error('image') <small class="text-danger">{{ $message }}</small> @enderror

            @if($food->image)
                <div class="mt-2">
                    <img src="{{ asset('img/' . $food->image) }}" alt="{{ __('admin.current_image') }}" width="150">
                </div>
            @endif
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-success">{{ __('admin.update_food') }}</button>
        <a href="{{ route('admin.foods') }}" class="btn btn-secondary">{{ __('admin.cancel') }}</a>
    </form>
</div>
@endsection
