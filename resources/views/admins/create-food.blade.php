@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-5 d-inline">{{ __('admin.create_food_items') }}</h5>
                        <form method="POST" action="{{ route('admin.foods.store') }}" enctype="multipart/form-data">
                            @csrf
                            <!-- Email input -->
                            <div class="form-outline mb-4 mt-4">
                                <input type="text" name="name" id="form2Example1" class="form-control"
                                    placeholder="{{ __('admin.placeholder_name') }}" />

                            </div>
                            <div class="form-outline mb-4 mt-4">
                                <input type="text" name="price" id="form2Example1" class="form-control"
                                    placeholder="{{ __('admin.placeholder_price') }}" />

                            </div>
                            <div class="form-outline mb-4 mt-4">
                                <input type="file" name="image" id="form2Example1" class="form-control" />

                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">{{ __('admin.description') }}</label>
                                <textarea class="form-control" name="description" id="exampleFormControlTextarea1" rows="3"></textarea>
                            </div>

                            <div class="form-outline mb-4 mt-4">

                                <select name="categories_id" class="form-control">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>


                            </div>

                            <br>



                            <!-- Submit button -->
                            <button type="submit" name="submit" class="btn btn-primary  mb-4 text-center">{{ __('admin.create') }}</button>


                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript"></script>
@endsection
