<!DOCTYPE html>
<html>
<head>
    <title>Parking System</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
</head>
<body>

<div class="container">
    <h1>Parking System</h1>
    <a class="btn btn-success mb-3 mt-1" href="javascript:void(0)" id="createNewProduct"> Create </a>
    <table class="table table-bordered data-table">
        <thead>
        <tr>
            <th>No</th>
            <th>Brand</th>
            <th>Model</th>
            <th>Namber</th>
            <th>Paid</th>
            <th>Color</th>
            <th>Details</th>
            <th width="280px">Action</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
            </div>
            <div class="modal-body">
                <form id="productForm" name="productForm" class="form-horizontal">
                    <input type="hidden" name="parking_id" id="parking_id">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Brand</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="brand" name="brand" placeholder="Volvo" value="" maxlength="50" required>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">mMdel</label>
                        <div class="col-sm-12">
                            <input id="model" name="model" required placeholder="XC90" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Phone</label>
                        <div class="col-sm-12">
                            <input id="number" name="number" required placeholder="+799999999999" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Color</label>

                        <div class="col-sm-12">
                            <select id="color" name="color" class="form-control">
                                <option value="" disabled selected>Choose your color</option>
                                <option value="red">Red</option>
                                <option value="green">Green</option>
                                <option value="blue">Blue</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <label for="paid" class="col-sm-2">Paid: </label>
                            <input value="paid" type="checkbox" id="paid" name="paid">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Comment</label>
                        <div class="col-sm-12">
                            <textarea id="comment" name="comment" required="" placeholder="Enter Details" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>

<script type="text/javascript">
    $(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('parkingAjax.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'car.brand', name: 'car.brand'},
                {data: 'car.model', name: 'car.model'},
                {data: 'car.number', name: 'car.number'},
                {data: 'paid', name: 'paid',
                    // render: function (data, type, row) {}
                    },
                {data: 'color', name: 'color'},
                {data: 'comment', name: 'comment'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $('#createNewProduct').click(function () {
            console.log('createNewParking');
            $('#product_id').val('');
            $('#productForm').trigger("reset");
            $('#modelHeading').html("Create New Parking");
            $('#ajaxModel').modal('show');
        });

        $('body').on('click', '.editParking', function () {
            var parking_id = $(this).data('id');
            $.get("{{ route('parkingAjax.index') }}" +'/' + parking_id +'/edit', function (data) {
                $('#modelHeading').html("Edit Parking");
                $('#ajaxModel').modal('show');
                $('#parking_id').val(parking_id);
                $('#brand').val(data.car.brand);
                $('#model').val(data.car.model);
                $('#number').val(data.car.number);
                $('#paid').prop('checked', data.paid);
                $('#color option[value="'+ data.car.color +'"]').prop('selected', true);
                $('#comment').val(data.comment);
            })
        });

        $('#saveBtn').click(function (e) {
            e.preventDefault();

            let data = {}, error = false;
            $.each($('#productForm').serializeArray(),
                function(i, v) {
                    data[v.name] = v.value;
                });

            if (!data.brand) {
                $('#brand').addClass('is-invalid');
                error = true;
            } else {
                $('#brand').removeClass('is-invalid');
            }

            if (!data.model) {
                $('#model').addClass('is-invalid');
                error = true;
            } else {
                $('#model').removeClass('is-invalid');
            }

            if (data.number && data.number.match(/^((8|\+7)[\- ]?)?(\(?\d{3,4}\)?[\- ]?)?[\d\- ]{5,10}$/gm) >= 0) {
                $('#number').removeClass('is-invalid');
            } else {
                $('#number').addClass('is-invalid');
                error = true;
            }

            if (!data.color) {
                $('#color').addClass('is-invalid');
                error = true;
            } else {
                $('#color').removeClass('is-invalid');
            }

            if (error) {
                return;
            }

            $.ajax({
                data: data,
                url: "{{ route('parkingAjax.store') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {

                    $('#productForm').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    table.draw();

                },
                error: function (data) {
                    var errors = $.parseJSON(data.responseText);
                    $.each(errors, function (key, value) {
                        $('#' + key).parent().addClass('error');
                    });

                }
            });
        });

        $('body').on('click', '.deleteParking', function () {

            var product_id = $(this).data("id");

            if (confirm("Are You sure want to delete !")) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('parkingAjax.store') }}"+'/'+product_id,
                    success: function (data) {
                        table.draw();
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

    });
</script>
</html>
