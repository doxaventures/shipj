<style>
.table-responsive tr > td:last-child {
    text-align: right;
}

.table-responsive tr >th:last-child {
    /* float: right; */
    text-align: right;
}
.text-center.messages {
    font-size: 18px;
    border: 1px solid green;
    width: 30%;
    margin: auto;
    padding: 10px 10px;
}
.cutom_inline{
    display: inline-block;
    margin-left: 10px;
}
</style>
@extends('inc.template')
@section('extend_template_details')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <h4 class="card-title">Orders Details</h4>
                        </div>

                                <div class="col-md-3">
                                    <form action="{{route('meta')}}" method="get">
                                    <div class="form-group">
                                        <div class="cutom_inline">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="sel1">Change Extra Percentage</label>
                                                </div>
                                                <div class="col-md-4">
                                                    <select class="form-control" id="sel1" name="meta_value">
                                                        <option value="5">5%</option>
                                                        <option value="10">10%</option>
                                                        <option value="15">15%</option>
                                                        <option value="20">20%</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="cutom_inline">
                                                        <input type="submit" class="btn btn-primary" value="Change">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>



                                    </div>
                                    </form>

                                </div>

                    </div>

                    @if(flash()->message)
                        <div class="text-center messages" style="text-align: center;">
                            <ul class="">
                                <li>
                                    {{ flash()->message }}</li>
                            </ul>

                        </div>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th class="border-top-0">Order ID</th>
                            <th class="border-top-0">Shipment STATUS</th>
                            <th class="border-top-0">Order Placed Date</th>
                            <th class="border-top-0">Order Total Price</th>
                            <th class="border-top-0">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($order as $order_details)
                        <tr>

                            <td class="txt-oflo">{{$order_details->order_id}}</td>
                            <td><span class="label label-info label-rounded">{{$order_details->shipment_status}}</span> </td>
                            <td class="txt-oflo">{{ Carbon\Carbon::parse($order_details->order_created_at)->format('l jS \\ F Y h:i:s A')}}</td>
                            <td><span class="font-medium">$ {{$order_details->total_charges}}</span></td>

                            <td><span style="padding-right: 10px;"><a href="{{route('get_single_order',$order_details->id)}}"><i class="fa fa-eye"></i></a></span><span><a href="#" data-toggle="modal" data-target="#exampleModal"> <i class="fa fa-trash" aria-hidden="true"></i></a></span></td>
                        </tr>
                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Cancel Shipment</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <h6>Are you sure? Shipment willbe cancelled</h6>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <a href="{{route('delete_shipment',$order_details->easy_shipment_id)}}" class="btn btn-danger">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection