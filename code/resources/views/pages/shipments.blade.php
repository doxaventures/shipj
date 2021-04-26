<style>
    .table-responsive tr > td:last-child {
        text-align: right;
    }

    .table-responsive tr >th:last-child {
        /* float: right; */
        text-align: right;
    }
</style>
@extends('inc.template')
@section('extend_template_details')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Shipment Details</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th class="border-top-0">Easy Ship ID</th>
                                <th class="border-top-0">Shipment Status</th>
                                <th class="border-top-0">Store Name</th>
                                <th class="border-top-0">Order Total Price</th>
                                <th class="border-top-0">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($ship as $order_details)
                                <tr>

                                    <td class="txt-oflo">{{$order_details->easy_shipment_id}}</td>
                                    <td><span class="label label-info label-rounded">{{$order_details->shipment_status}}</span> </td>
                                    <td class="txt-oflo">{{$order_details->store_name}}</td>
                                    <td><span class="font-medium">$ {{$order_details->total_charges}}</span></td>

                                    <td><span><a href="#" data-toggle="modal" data-target="#exampleModal"> <i class="fa fa-trash" aria-hidden="true"></i></a></span></td>
                                </tr>


                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection