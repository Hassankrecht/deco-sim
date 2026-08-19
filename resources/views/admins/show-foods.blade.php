@extends('layouts.admin')
@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4 d-inline">{{ __('admin.foods') }}</h5>
                        <a href="{{ route('admin.foods.create') }}"
                            class="btn btn-primary mb-4 text-center float-right">{{ __('admin.create_foods') }}</a>
                            <a href="{{ route('admin.category.create') }}" style="margin-right: 10px;"
                            class="btn btn-primary mb-4 text-center float-right">{{ __('admin.add_category') }}</a>
                            <a href="{{ route('admin.foods.create') }}" style="margin-right: 10px;"
                            class="btn btn-primary mb-4 text-center float-right">{{ __('admin.edit_category') }}</a>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('admin.hash') }}</th>
                                    <th scope="col">{{ __('admin.name') }}</th>
                                    <th scope="col">{{ __('admin.image') }}</th>
                                    <th scope="col">{{ __('admin.category') }}</th>
                                    <th scope="col">{{ __('admin.description') }}</th>
                                    <th scope="col">{{ __('admin.price') }}</th>
                                    <th scope="col">{{ __('admin.edit') }}</th>
                                    <th scope="col">{{ __('admin.delete') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($foods as $index => $food)
                                    @csrf
                                    <tr>
                                        <th scope="row">{{ $index + 1 }}</th>
                                        <td>{{ $food->name }}</td>
                                        <td><img src="{{ asset('img/' . $food->image . '') }}" alt="{{ $food->name }}"
                                                width="50">

                                        </td>
                                        <td>{{ $food->category_id }}</td>
                                        <td>{{ $food->description }}</td>
                                        <td>{{ \App\Support\Currency::format($food->price) }}</td>
                                        <td>
                                            <button class="btn btn-warning"><a
                                                    href="{{ route('admin.foods.edit', $food->id) }}"
                                                    class="text-white">{{ __('admin.edit') }}</a></button>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.foods.delete', $food->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf

                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger"
                                                    onclick="return confirm('{{ __('admin.are_you_sure') }}')">{{ __('admin.delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>



    </div>
    <script type="text/javascript"></script>
@endsection
