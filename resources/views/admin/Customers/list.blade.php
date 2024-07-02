@extends('layouts.web')
@push('css')

<link rel="stylesheet" href="{{ asset('web/mycss/mycss.css') }}">
<!--datatable css-->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
<!--datatable responsive css-->
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<!-- Bootstrap Css -->
@endpush
@section('title')
Customers List
@endsection
@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <!-- Title Section -->
                    <h5 class="card-title mb-0 col-sm-8 col-md-10">
                        Customers List
                    </h5>

                    <!-- Buttons Section -->
                    <div class="col-sm-4 col-md-2 d-flex justify-content-end">

                        <button type="submit" class="btn btn-outline-primary btn-icon waves-effect waves-light"
                            id="refresh">
                            <i class="ri-24-hours-fill"></i>
                        </button>
                    </div>
                </div>
            </div>


            <div class="card-body" style="overflow:auto">
                <table id="alternative-pagination"
                    class="table nowrap dt-responsive align-middle table-hover table-bordered"
                    style="width:100%;overflow: scroll">
                    <thead>
                        <tr>
                            <th scope="row">#SSL</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Adress</th>
                            <th>Image ID Front</th>
                            <th>Image ID Back</th>
                            <th>Is verified (phone)</th>
                            <th>Is approve id</th>

                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--end col-->
</div>
<!--end row-->


@endsection
@push('js')
<script>
 var table = $('#alternative-pagination').DataTable({
    ajax: '{{ route('customer.dataTable') }}',
    columns: [
        {
            'data': null,
            render: function(data, type, row, meta) {
                return meta.row + 1;
            }
        },
        { 'data': 'name' },
        {
            'data': 'email',
            defaultContent: "Not provided"
        },
        { 'data': 'phone' },
        {
            'data': 'gender',
            defaultContent: "Not provided"
        },
        {
            'data': 'address',
            defaultContent: "Not provided"
        },
        {
            'data': null,
            render: function(data, type, row) {
                return `
                    <img src="{{ asset('/') }}/${data.image_id_front}"
                    class="small-image" style="height: 50px; width: 50px" onclick="openFullScreen(this)">
                `;
            }
        },
        {
            'data': null,
            render: function(data, type, row) {
                return `
                    <img src="{{ asset('/') }}/${data.image_id_back}"
                    class="small-image" style="height: 50px; width: 50px" onclick="openFullScreen(this)">
                `;
            }
        },
        {
            'data': null,
            render: function(data) {
                let status = data.is_verified == 1 ? 'No' : 'Yes';
                let editUrl = '{{ route('customer.is_verified', ':id') }}'.replace(':id', data.id);
                return `<a href="#" class="btn  btn-primary" onclick="confirmDelete('${editUrl}')">${status}</a>`;
            }
        },
        {
            'data': null,
            render: function(data) {
                let status = data.is_approve_id == 1 ? 'No' : 'Yes';
                let editUrl = '{{ route('customer.is_approve_id', ':id') }}'.replace(':id', data.id);
                return `<a href="#" class="btn  btn-secondary" onclick="confirmDelete('${editUrl}')">${status}</a>`;
            }
        },
        {
            'data': 'created_at',
            render: function(data, type, row) {
                let date = new Date(data);
                if (!isNaN(date.getTime())) {
                    let year = date.getFullYear();
                    let month = (date.getMonth() + 1).toString().padStart(2, '0');
                    let day = date.getDate().toString().padStart(2, '0');
                    return `${year}-${month}-${day}`;
                } else {
                    return 'لا يجود بيانات';
                }
            }
        },
    ]
});

$('#refresh').on('click', function() {
    $('#alert').css('display', 'none');
    table.ajax.reload();
});

function confirmDelete(url) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't change status!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

</script>
<script>
    function openFullScreen(image) {
            var fullScreenContainer = document.createElement('div');
            fullScreenContainer.className = 'fullscreen-image';

            var fullScreenImage = document.createElement('img');
            fullScreenImage.src = image.src;

            fullScreenContainer.appendChild(fullScreenImage);
            document.body.appendChild(fullScreenContainer);

            fullScreenContainer.addEventListener('click', function() {
                document.body.removeChild(fullScreenContainer);
            });
        }
</script>

@endpush
