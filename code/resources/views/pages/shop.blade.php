@extends('inc.main')
@section('content')

    <div class="container mt-lg-3 custom_class">
        <form action="{{route('shop')}}" method="get">
            <div class="form-group">
                <label for="email">Shop URL</label>
                <input type="text" class="form-control" id="email" name="shop">
                <div class="form-group">
                    <input type="submit"  placeholder="Enter Shop Url" class="btn btn-block btn-primary">
                </div>
            </div>
        </form>
    </div>
    @endsection