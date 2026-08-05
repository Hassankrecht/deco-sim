@extends('layouts.admin')
|@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4 d-inline">Orders</h5>

                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">name</th>
                                    <th scope="col">email</th>
                                    <th scope="col">town</th>
                                    <th scope="col">country</th>
                                    <th scope="col">zipcode</th>
                                    <th scope="col">phone_number</th>
                                    <th scope="col">address</th>
                                    <th scope="col">total_price</th>
                                    <th scope="col">status</th>
                                    <th scope="col">delete</th>
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
                                                    onclick="return confirm('Are you sure?')">Delete</button>
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
