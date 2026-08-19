@extends('layouts.admin')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-5 d-inline">{{ __('admin.create_food_category') }}</h5>
                        <form method="POST" action="{{ route('admin.category.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="form-outline mb-4 mt-4">
                                <input type="text" name="name" id="form2Example1" class="form-control"
                                    placeholder="{{ __('admin.placeholder_name') }}" />

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
