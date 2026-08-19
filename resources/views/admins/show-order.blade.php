@extends('layouts.admin')
|@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4 d-inline">{{ __('admin.orders') }}</h5>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('admin.hash') }}</th>
                                    <th scope="col">{{ __('admin.name') }}</th>
                                    <th scope="col">{{ __('admin.email') }}</th>
                                    <th scope="col">{{ __('admin.town') }}</th>
                                    <th scope="col">{{ __('admin.country') }}</th>
                                    <th scope="col">{{ __('admin.zipcode') }}</th>
                                    <th scope="col">{{ __('admin.phone') }}</th>
                                    <th scope="col">{{ __('admin.address') }}</th>
                                    <th scope="col">{{ __('admin.total_price') }}</th>
                                    <th scope="col">{{ __('admin.status') }}</th>
                                    <th scope="col">{{ __('admin.delete') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $index => $order)
                                    @csrf
                                    <tr>
                                        <th scope="row">{{ $index + 1 }}</th>
                                        <td>{{ $order->name }}</td>
                                        <td>{{ $order->email }}</td>
                                        <td>{{ $order->town }}</td>
                                        <td>{{ $order->country }}</td>
                                        <td>{{ $order->zipcode }}</td>
                                        <td>{{ $order->phone_number }}</td>
                                        <td>{{ $order->address }}</td>
                                        <td>{{ \App\Support\Currency::format($order->total_price) }}</td>

                                        <td>{{ $order->status }}</td>

                                        <td>
                                            <form action="{{ route('admin.orders.delete', $order->id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('{{ __('admin.are_you_sure') }}')">{{ __('admin.delete') }}</button>
                                            </form>
                                        </td>
                                        
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
