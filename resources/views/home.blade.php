@extends('layouts.app')

{{-- start of user homepage --}}
@section('user')
    {{-- user navbar --}}
    <nav class="glass navbar navbar-expand-md z-5" style="position: sticky; top:0px;">
        <div class="container d-flex justify-content-between">
            <a class="navbar-brand text-white" href="{{ url('/') }}">J&G</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <i class="bi bi-person-fill text-white p-2 m-2" role="button" id="logoutButton"></i>
                        {{-- <i class="bi bi-bag-fill text-white p-2 m2" role="button"></i> --}}
                        <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    {{-- start of product displays --}}
    <div class="my-5">
        <section class="productDisplayContainer">
            <div class="rowImg mx-5" id="productContainer">
                {{-- note self: use ternary here if on sale ? display original price with strike + display sale price  :  display original price --}}
                {{-- put ribbon if sale --}}
                {{-- sale x featured || if sale pput sale ribbon, if featured put featured ribbon, if both put featuredxsale ribbon --}}
                @foreach ($products as $product)
                    @if ($product->isAvailable == 'Available')
                        <figure class="figure product-card" data-price="{{ $product->price }}"
                            data-category="{{ $product->category }}" data-isFeatured="{{ $product->isFeatured }}">
                            {{-- if sale & featured --}}
                            {{-- <div class="ribbon ribbon-top-left"><span>Exclusive</span></div> --}}
                            {{-- elseif sale/ --}}
                            {{-- <div class="ribbon ribbon-top-left"><span>On Sale</span></div> --}}
                            @if ($product->isFeatured == 'Featured')
                                <div class="ribbon ribbon-top-left"><span>Featured</span></div>
                            @endif

                            <img src="/storage/images/{{ $product->imageUpload }}" alt="Product Image"
                                style="width: 280px; height: 250px;">
                            <h3 class="card-title text-white text-truncate text-center" style="max-width: 350px;">
                                {{ $product->productName }}</h3>
                            <p class="card-text text-white text-center">Php {{ number_format($product->price, 2) }}</p>
                            <figcaption class="text-center">
                                <p>{{ $product->productDescription }}</p>
                                <button class="buyNow btn btn-primary text-white mt-5" id="{{ $product->id }}"
                                    data-id="{{ $product->id }}" data-price="{{ $product->price }}"
                                    data-productName="{{ $product->productName }} "
                                    data-isFeatured="{{ $product->isFeatured }}">
                                    BuyNow
                                </button>
                            </figcaption>
                        </figure>
                    @endif
                @endforeach
            </div>
        </section>
    </div>
    {{-- end of product display --}}


    {{-- start of user functions --}}
    <div class="glass text-white" style="width:250px; position: fixed; top:120px; right:20px;">
        <div class="dropdown justify-content-center align-items-center d-flex flex-column">
            <p>Search</p>
            <input id="search" type="text" class="form-control mb-5"name="search" style="width: 160px;" autofocus>
            <p>Sort By Price</p>
            <select id="sortSelect" class="form-control glass d-flex mb-5" style="width: 160px;">
                <option value="price-asc">Lowest to Highest</option>
                <option value="price-desc">Highest to Lowest</option>
            </select>
            <p>Guitar Category</p>
            <div class="mb-5 input-group-prepend justify-content-center align-items-start d-flex flex-column">
                <div class="input-group-text glass" style="width: 160px;">
                    <input type="checkbox" aria-label="Checkbox for following text input" class="guitarFilter"
                        id="acousticCheck" value="Acoustic">&nbsp;&nbsp;Acoustic
                </div> <br>
                <div class="input-group-text glass" style="width: 160px;">
                    <input type="checkbox" aria-label="Checkbox for following text input" class="guitarFilter"
                        id="electricCheck" value="Electric">&nbsp;&nbsp;Electric
                </div> <br>
                <div class="input-group-text glass" style="width: 160px;">
                    <input type="checkbox" aria-label="Checkbox for following text input" class="guitarFilter"
                        id="bassCheck" value="Bass">&nbsp;&nbsp;Bass
                </div>
            </div>
            <p>Hot Deals</p>
            <div class="input-group-text glass" style="width: 160px;">
                <input type="checkbox" id="isFeaturedCheck"
                    aria-label="Checkbox for following text input">&nbsp;&nbsp;Featured
            </div> <br>
            <div class="input-group-text glass" style="width: 160px;">
                <input type="checkbox" aria-label="Checkbox for following text input">&nbsp;&nbsp;On Sale
            </div> <br>
        </div>
    </div>
    {{-- end of user functions --}}

    <script src="{{ mix('js/app.js') }}"></script>
    <script>
        $(document).ready(function() {

            // user logout
            $('#logoutButton').on('click', function(e) {
                event.preventDefault(e);

                Swal.fire({
                    title: `{{ Auth::user()->name }}`,
                    text: "Are you sure you want to logout?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#logoutForm').submit();
                    }
                });
            });

            // hide/show ribbbon on hover
            $('#productContainer').on({
                mouseenter: function() {
                    // console.log('entered');
                    $(this).find('.ribbon').css('display', 'none');
                    $(this).css('transform', 'scale(1.05)');
                },
                mouseleave: function() {
                    // console.log('leave');
                    $(this).find('.ribbon').css('display', 'block');
                    $(this).css('transform', 'scale(1)');
                }
            }, '.figure');

            //buy now button || send the order || ajax
            $('#productContainer').on('click', '.buyNow', function(e) {
                console.log('button clicked');
                var productId = this.getAttribute('data-id');
                var productName = this.getAttribute('data-productName');
                var price = this.getAttribute('data-price');
                const formData = new FormData();
                formData.append('productName', productName);
                formData.append('price', price);
                formData.append('id', productId);
                formData.append('status', 'Delivered');
                Swal.fire({
                    title: "Are you sure?",
                    text: `Do you want to buy ${productName} with a price of Php${price.toLocaleString()}?`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Buy!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrfToken = $('meta[name="csrf-token"]').attr('content');
                        $.ajax({
                            url: '/home/addOrder',
                            type: 'POST',
                            contentType: false,
                            processData: false,
                            data: formData,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken // Add CSRF token to headers
                            },
                            success: function(response) {
                                console.log(response);
                            },
                            error: function(xhr, status, error) {
                                // Handle errors here
                                alert('An error occurred: ' + error);
                            }
                        });
                        Swal.fire({
                            title: "Cheers!",
                            text: `You Successfully Bought ${productName}`,
                            icon: "success"
                        });
                    }
                });
            });

            // search function
            $('#search').on('keyup', function() {
                var searchTerm = $(this).val().toLowerCase();
                // console.log(searchTerm);
                $('.product-card').each(function() {
                    var productName = $(this).find('.card-title').text().toLowerCase();
                    if (productName.indexOf(searchTerm) === -1) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            });

            // sort by price
            sortProducts('price-asc');

            $('#sortSelect').change(function() {
                let sortValue = $(this).val();
                sortProducts(sortValue);

            });

            function sortProducts(sortOrder) {
                let products = $('.product-card');

                products.sort(function(a, b) {
                    let priceA = parseFloat($(a).data('price'));
                    let priceB = parseFloat($(b).data('price'));

                    if (sortOrder === 'price-desc') {
                        return priceB - priceA;
                    } else {
                        return priceA - priceB;
                    }
                });
                $('#productContainer').html(products);
            }

            // sort by category
            $('.guitarFilter').change(function() {
                filterProducts();
            });

            function filterProducts() {
                // console.log('changed');
                let selectedCategories = [];
                $('.guitarFilter:checked').each(function() {
                    selectedCategories.push($(this).val());
                });
                $('.product-card').each(function() {
                    let productCategory = $(this).data('category');
                    if (selectedCategories.length === 0 || selectedCategories.includes(productCategory)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
            // shows all data if no seleceted
            filterProducts();


            // filter: featured/not featured
            $('#isFeaturedCheck').on('change', function() {

                let isChecked = $(this).is(':checked');
                // console.log(isChecked);

                $('.product-card').each(function() {
                    let isFeatured = $(this).attr('data-isFeatured');
                    if (isChecked && isFeatured === 'Not Featured') {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            });






        });
    </script>
@endsection
{{-- end of user homepage --}}


{{-- start of admin homepage --}}
@section('admin')
    <div class="container-fluid pt-5">
        <div class="row d-flex justify-content-between">
            {{-- start of dashboard functions --}}
            <div class="glass d-flex flex-column" style="height: 90vh; position: fixed; top:20px; width: 300px;">
                <div class="row d-flex justify-content-center align-items-center pt-5 pb-5">
                    <img src="{{ asset('img/logo/jAndGLogo.png') }}" alt="logo" style="width:180px;">
                </div>
                <div>
                    <button class="btn glass w-100 p-3 mt-3" onclick="showSection('dashboard')">Dashboard</button>
                    <button class="btn glass w-100 p-3 mt-3" onclick="showSection('products')">Products</button>
                    <button class="btn glass w-100 p-3 mt-3" onclick="showSection('orders')">Orders</button>
                    <button class="btn glass w-100 p-3 mt-3" id="logoutButton">Logout</button>
                    <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
            {{-- end of dashboard functions --}}
        </div>

        {{-- start of dashboard section --}}
        <div id="dashboard" class="adminSections col-12 glass"
            style="height: 90vh; position: fixed; top:20px; left:330px; width: 1500px;">
            <div class="container-fluid h-100">
                <div class="row h-50">
                    <div class="col-6 dashboard-section d-flex justify-content-center">
                        <div class="d-flex align-items-center">
                            <p class="totalProducts text-white display-1"></p>
                        </div>
                    </div>
                    <div class="col-6 dashboard-section d-flex justify-content-center">
                        <div class="d-flex align-items-center">
                            <p class="totalOrders text-white display-1"></p>
                        </div>
                    </div>
                </div>
                <div class="row h-50">
                    <div class="col-6 graph-container dashboard-section">
                        <div id="barGraph" style="width: 100%; height: 100%;">
                            <!-- Content for Bar Graph -->
                        </div>
                    </div>
                    <div class="col-6 chart-container dashboard-section">
                        <div id="pieChart" style="width: 100%; height: 100%;">
                            <!-- Content for Pie Chart -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- end of dashboard section --}}

        {{-- start of product section --}}
        <div id="products" class="adminSections col-8 glass"
            style="display: none; position: fixed; top:20px; left:330px; width: 1500px;">
            {{-- start of admins product section --}}
            <div class="row px-5 py-2 d-flex flex-grow-1 ">
                <i class="bi bi-bell-fill d-flex justify-content-end text-white m-2"></i>
            </div>

            <div class="row px-5 py-4 flex-grow-1 justify-content-center align-items-center ">
                <div class="input-group mb-3 d-flex flex-column">
                    <div>
                        <p class="totalProducts text-white"></p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div>
                                <input class="glass p-2" type="date" name="" id="productDateInput">
                            </div>
                        </div>
                        <div>
                            <button class="btn glass text-white" data-bs-toggle="modal" data-bs-target="#addProduct">Add
                                Product</button>
                            <button class="btn glass text-white" data-bs-toggle="modal" data-bs-target="#addToSale"
                                id="productAddToSale" style="display: none;">Add To Sale</button>
                            <button class="btn btn-danger text-white" id="productDelete"
                                style="display: none;">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row my-5 px-5 py-2 d-flex flex-grow-1">
                <table id="productTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Pick</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Product Description</th>
                            <th scope="col">Picture</th>
                            <th scope="col">Stocks</th>
                            <th scope="col">Price</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

        </div>
        {{-- end of product section --}}

        {{-- start of orders section --}}
        <div id="orders" class="adminSections col-8 glass"
            style="display: none; position: fixed; top:20px; left:330px; width: 1500px;">
            <div class="row px-5 py-2 d-flex flex-grow-1 ">
                <i class="bi bi-bell-fill d-flex justify-content-end text-white m-2"></i>
            </div>

            <div class="row px-5 py-4 flex-grow-1 justify-content-center align-items-center ">
                <div class="input-group mb-3 d-flex flex-column">
                    <div>
                        <p class="totalOrders text-white"></p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <div>
                                <input class="glass p-2" type="date" name="" id="orderDateInput">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row my-5 px-5 py-2 d-flex flex-grow-1">
                <table id="ordersTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            {{-- <th scope="col"><input type="checkbox"></th> --}}
                            <th scope="col">Product</th>
                            <th scope="col">Customer</th>
                            <th scope="col">Purchase Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Price</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        {{-- end of orders section --}}


        
        {{-- start of add product modal --}}
        <div class="modal fade" id="addProduct" tabindex="-1" aria-labelledby="addProduct" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content  glass">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductLabel">Add Product</h5>
                        <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>


                    <div class="modal-body" id="addProduct">
                        <form method="post" id="productForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="productName" class="form-label">Product Name</label>
                                <input name="productName" type="text"
                                    class="form-control @error('productName') is-invalid @enderror" id="productName"
                                    aria-describedby="emailHelp" required>
                                @error('productName')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="productDescription" class="form-label">Product Description</label>
                                <input name="productDescription" type="text" class="form-control"
                                    id="productDescription" required>
                            </div>
                            <div class="mb-3 pb-1">
                                <label for="price" class="form-label">Price</label>
                                <input name="price" type="number"
                                    class="form-control @error('price') is-invalid @enderror" id="price" required
                                    min="0" step="0.01">
                                @error('price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mt-3 pt-3">
                                <input name="imageUpload" type="file" class="form-control" id="imageUpload"
                                    accept="image/*" required>
                                <div class="d-flex justify-content-center py-3">
                                    <img id="imagePreview" src="" alt="Image Preview"
                                        style="display: none; max-width: 100px; height: 100px;">
                                </div>
                            </div>
                            <div class="mb-3 pt-3">
                                <select name="isFeatured" class="form-select" aria-label="Default select example">
                                    <option value="Featured">Featured</option>
                                    <option value="Not Featured">Not Featured</option>
                                </select>
                            </div>
                            <div class="mb-3 pt-3">
                                <select name="isAvailable" class="form-select" aria-label="Default select example">
                                    <option value="Available">Available</option>
                                    <option value="Not Available">Not Available</option>
                                </select>
                            </div>
                            <div class="mb-3 pt-3">
                                <select name="category" class="form-select" aria-label="Default select example">
                                    <option value="Acoustic">Acoustic</option>
                                    <option value="Electric">Electric</option>
                                    <option value="Base">Base</option>
                                </select>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Add Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- end of add product modal --}}

        {{-- start of add to sale modal --}}
        <div class="modal fade" id="addToSale" tabindex="-1" aria-labelledby="addToSale" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content  glass">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addToSaleLabel">Add Product On Sale</h5>
                        <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>


                    <div class="modal-body" id="addToSale">
                        <form method="post" id="addToSaleForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input name="date" type="date" class="form-control" id="date"
                                    aria-describedby="emailHelp">
                            </div>
                            <div class="mb-3">
                                <label for="discount" class="form-label">Discount</label>
                                <input name="discount" type="text" class="form-control" id="discount">
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Add To Sale</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- end of add product modal --}}

        {{-- start of edit button modal --}}
        <div class="modal fade" id="editProductBtn" tabindex="-1" aria-labelledby="editProductBtn" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content  glass">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductBtnLabel"></h5>
                        <button type="button" class="btn-close btn-danger" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>


                    <div class="modal-body" id="editProductForm">
                        <form id="editProductBtnForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="productName" class="form-label">Product Name</label>
                                <input name="editproductName" id="editProductName" type="text" class="form-control"
                                    id="productName" aria-describedby="emailHelp">
                            </div>
                            <div class="mb-3">
                                <label for="productDescription" class="form-label">Product Description</label>
                                <input name="editProductDescription" id="editProductDescription" type="text"
                                    class="form-control" id="productDescription">
                            </div>
                            <div class="mb-3 pb-1">
                                <label for="price" class="form-label">Price</label>
                                <input name="price" id="editPrice" type="text" class="form-control"
                                    id="price">
                            </div>
                            <div class="mb-3  pb-3">
                                <input name="editImageUpload" id="editImageUpload" type="file" class="form-control"
                                    accept="image/*">
                                <div class="d-flex justify-content-center py-3">
                                    <img id="editImagePreview" src="" alt="Image Preview"
                                        style="display: none; max-width: 100px; height: 100px;">
                                </div>
                            </div>
                            <div class="mb-3 pt-3">
                                <select name="editIsFeatured" id="editIsFeatured" class="form-select"
                                    aria-label="Default select example">
                                    <option value="Featured">Featured</option>
                                    <option value="Not Featured">Not Featured</option>
                                </select>
                            </div>
                            <div class="mb-3 pt-3">
                                <select name="editIsAvailable" id="editIsAvailable" class="form-select"
                                    aria-label="Default select example">
                                    <option value="Available">Available</option>
                                    <option value="Not Available">Not Available</option>
                                </select>
                            </div>
                            <div class="mb-3 pt-3">
                                <select name="editCategory" id="editCategory" class="form-select"
                                    aria-label="Default select example">
                                    <option value="Acoustic">Acoustic</option>
                                    <option value="Electric">Electric</option>
                                    <option value="Bass">Bass</option>
                                </select>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="editProductBtn btn btn-primary">Edit Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- end of edit button modal --}}

            {{-- end of admins product section --}}

        </div>
        {{-- end of edit button modal --}}



    </div>

    <script src="{{ mix('js/app.js') }}"></script>

    <script>
        // shows the section i want on admin section
        function showSection(sectionId) {

            const sections = document.querySelectorAll('.adminSections');

            sections.forEach(section => {
                section.style.display = 'none';
            });

            const sectionToShow = document.getElementById(sectionId);
            if (sectionToShow) {
                sectionToShow.style.display = 'block';
            }
        }

        $(document).ready(function() {

            // admin logout
            $('#logoutButton').on('click', function(e) {
                event.preventDefault(e);

                Swal.fire({
                    title: "Logging Out?",
                    text: "Are you sure you want to logout?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#logoutForm').submit();
                    }
                });
            });

            // set default date on date input to today
            var today = new Date();

            var yyyy = today.getFullYear();
            var mm = ('0' + (today.getMonth() + 1)).slice(-2);
            var dd = ('0' + today.getDate()).slice(-2);

            var formattedDate = yyyy + '-' + mm + '-' + dd;

            $('#orderDateInput').val(formattedDate);

            $('#productDateInput').val(formattedDate);

            // image preview
            $('#imageUpload').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#imagePreview');

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    }

                    reader.readAsDataURL(file);
                } else {
                    imagePreview.hide();
                }
            });
            // edit image preview
            $('#editImageUpload').on('change', function(event) {
                const file = event.target.files[0];
                const imagePreview = $('#editImagePreview');

                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result).show();
                    }

                    reader.readAsDataURL(file);
                } else {
                    imagePreview.hide();
                }
            });
            // sending product form via ajax || add product
            $('#productForm').on('submit', function(e) {
                event.preventDefault(e);

                const formData = new FormData(this);
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '/home/addProduct',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken // Add CSRF token to headers
                    },
                    success: function(response) {
                        // console.log(response);
                        $('#addProduct').modal('hide');
                        $('#productForm')[0].reset();
                        $('#productTable').DataTable().ajax.reload(null, false);
                        pieChart();
                        barGraph();
                        Swal.fire({
                            icon: "success",
                            title: "Product has been added",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    },
                    error: function(xhr, status, error) {
                        // Handle errors here
                        // alert('An error occurred: ' + error);
                    }
                });
            });
            // edit products via ajax || edit product
            $(document).on('click', '.editBtn', function() {

                var Id = this.getAttribute('data-id');
                var productName = this.getAttribute('data-productName');
                var productDescription = this.getAttribute('data-productDescription');
                var imageUpload = this.getAttribute('data-imageUpload');
                var isAvailable = this.getAttribute('data-isAvailable');
                var isFeatured = this.getAttribute('data-isFeatured');
                var price = this.getAttribute('data-price');

                // Populate form fields
                $('#editProductBtnLabel').text('Editing ' + productName);
                $('#editProductName').val(productName);
                $('#editProductDescription').val(productDescription);
                $('#editPrice').val(price);


                // Update image preview if an image URL is provided
                if (imageUpload) {
                    // console.log('true')
                    $('#editImagePreview').attr('src', '/storage/images/' + imageUpload).show();
                } else {
                    $('#editImagePreview').hide();
                }

                // Set the select options to the values of isFeatured and isAvailable
                $('select[name="isFeatured"]').val(isFeatured);
                $('select[name="isAvailable"]').val(isAvailable);

                // Show the modal
                $('#editProductBtn').modal('show');

                $('#editProductBtnForm').on('submit', function(e) {
                    event.preventDefault(e);
                    const formData = new FormData(this);
                    formData.append('id', Id);
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');

                    Swal.fire({
                        title: "Save Edited Product?",
                        text: "",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '/home/editProduct',
                                type: 'POST',
                                contentType: false,
                                processData: false,
                                data: formData,
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken // Add CSRF token to headers
                                },
                                success: function(response) {
                                    console.log(response);
                                    $('#productTable').DataTable().ajax.reload(
                                        null, false);
                                    pieChart();
                                    barGraph();
                                },
                                error: function(xhr, status, error) {
                                    console.log('An error occurred: ' + error);
                                }
                            });
                            Swal.fire({
                                title: "Edited!",
                                text: "Product has been edited",
                                icon: "success"
                            });
                        }
                    });
                });

            });

            // product tables x datatables
            var productTable = $('#productTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/home/showProduct',
                    type: 'GET',
                    dataType: 'json'
                },
                columns: [{
                        data: null,
                        name: 'check',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<input type="checkbox" class="productCheckBox" value="${row.id}" data-productName="${row.productName}">`;
                        }
                    },
                    {
                        data: 'productName',
                        name: 'productName'
                    },
                    {
                        data: 'productDescription',
                        name: 'productDescription',
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).addClass('text-truncate w-50');
                        }
                    },
                    {
                        data: 'imageUpload',
                        name: 'imageUpload',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            var imageUrl = '/storage/images/' + data;
                            return '<img src="' + imageUrl +
                                '" width="30px" height="30px" alt="Image">';
                        }
                    },
                    {
                        data: 'isAvailable',
                        name: 'isAvailable',
                        render: function(data, type, row) {
                            var textColor = data === 'Available' ? 'green' : 'red';
                            return '<span style="color: ' + textColor + ';">' + data + '</span>';
                        }
                    },
                    {
                        data: 'price',
                        name: 'price',
                    },
                    {
                        data: 'isFeatured',
                        name: 'isFeatured',
                        render: function(data, type, row) {
                            var textColor = data === 'Featured' ? 'green' : 'red';
                            return '<span style="color: ' + textColor + ';">' + data + '</span>';
                        }
                    },
                    {
                        data: null,
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-primary editBtn text-white" 
                                data-id="${row.id}" 
                                data-productName="${row.productName}"
                                data-productDescription="${row.productDescription}"
                                data-imageUpload="${row.imageUpload}"
                                data-isAvailable="${row.isAvailable}"
                                data-isFeatured="${row.isFeatured}"
                                data-price="${row.price}"
                                data-bs-toggle="modal"
                                data-bs-target="#editProductBtn">Edit</button>`;
                        }
                    },


                ]
            });
            // display total rows for products
            productTable.on('draw', function() {
                let totalProduct = productTable.ajax.json().recordsTotal;
                $('.totalProducts').text('Total Products: ' + totalProduct);
            });

            // order tables x datatables
            var orderTable = $('#ordersTable').DataTable({
                processing: true,
                // serverSide: false,
                ajax: {
                    url: '/home/showOrders',
                    type: 'get',
                    dataType: 'json'
                },
                columns: [

                    {
                        data: 'productName',
                        name: 'product'
                    },
                    {
                        data: 'customer',
                        name: 'customer'
                    },
                    {
                        data: 'created_at',
                        name: 'purchaseDate',
                    },
                    {
                        data: 'status',
                        name: 'status',
                    },
                    {
                        data: 'price',
                        name: 'price',
                    },
                ]
            });
            // display total rows for order
            orderTable.on('draw', function() {
                let totalOrder = orderTable.ajax.json().recordsTotal;
                $('.totalOrders').text('Total Orders: ' + totalOrder);
            });

            // high charts pie raph
            $.ajax({
                url: '/home/orderData',
                type: 'GET',
                // dataType: 'json',
                success: function(response) {
                    // console.log(response);
                    const productName = [];
                    const data = [];

                    response.forEach(item => {
                        productName.push(item.productName);
                        data.push(item.count);
                    });

                    const formattedData = productName.map((name, index) => ({
                        name: name,
                        y: data[index]
                    }));

                    function pieChart() {
                        Highcharts.chart('pieChart', {
                            chart: {
                                type: 'pie',
                                backgroundColor: 'transparent'
                            },
                            title: {
                                text: 'Orders Chart'
                            },
                            tooltip: {
                                valueSuffix: ''
                            },
                            subtitle: {
                                text: 'Orders'
                            },
                            plotOptions: {
                                series: {
                                    allowPointSelect: true,
                                    cursor: 'pointer',
                                    dataLabels: [{
                                        enabled: true,
                                        distance: 20
                                    }, {
                                        enabled: true,
                                        distance: -40,
                                        format: '{point.percentage:.1f}%',
                                        style: {
                                            fontSize: '1.2em',
                                            textOutline: 'none',
                                            opacity: 0.7
                                        },
                                        filter: {
                                            operator: '>',
                                            property: 'percentage',
                                            value: 10
                                        }
                                    }]
                                }
                            },
                            series: [{
                                name: 'Total',
                                colorByPoint: true,
                                data: formattedData,
                            }]
                        });
                    }
                    pieChart();
                },
                error: function(xhr, status, error) {
                    $('#result').html('<p>Error: ' + error + '</p>');
                }
            });

            // high charts bar graph
            $.ajax({
                url: '/home/orderData',
                type: 'GET',
                // dataType: 'json',
                success: function(response) {
                    // console.log(response);
                    const productName = [];
                    const data = [];

                    response.forEach(item => {
                        productName.push(item.productName);
                        data.push(item.count);
                    });

                    const formattedData = productName.map((name, index) => [name, data[index]]);

                    function barGraph() {
                        Highcharts.chart('barGraph', {
                            chart: {
                                type: 'column',
                                backgroundColor: 'transparent'
                            },
                            title: {
                                text: 'Product Sales'
                            },
                            subtitle: {
                                text: 'Counts of our total product sold'
                            },
                            xAxis: {
                                type: 'category',
                                labels: {
                                    autoRotation: [-45, -90],
                                    style: {
                                        fontSize: '13px',
                                        fontFamily: 'Verdana, sans-serif'
                                    }
                                }
                            },
                            yAxis: {
                                min: 0,
                                title: {
                                    text: 'Sold'
                                }
                            },
                            legend: {
                                enabled: false
                            },
                            tooltip: {
                                pointFormat: 'Available: <b>{point.y:.1f}</b>'
                            },
                            series: [{
                                name: 'Sold',
                                colors: [
                                    '#9b20d9', '#9215ac', '#861ec9', '#7a17e6',
                                    '#7010f9', '#691af3',
                                    '#6225ed', '#5b30e7', '#533be1', '#4c46db',
                                    '#4551d5', '#3e5ccf',
                                    '#3667c9', '#2f72c3', '#277dbd', '#1f88b7',
                                    '#1693b1', '#0a9eaa',
                                    '#03c69b', '#00f194'
                                ],
                                colorByPoint: true,
                                groupPadding: 0,
                                data: formattedData,
                                dataLabels: {
                                    enabled: true,
                                    rotation: -90,
                                    color: '#FFFFFF',
                                    inside: true,
                                    verticalAlign: 'top',
                                    format: '{point.y:.1f}', // one decimal
                                    y: 10, // 10 pixels down from the top
                                    style: {
                                        fontSize: '13px',
                                        fontFamily: 'Verdana, sans-serif'
                                    }
                                }
                            }]
                        });
                    }
                    barGraph();
                },
                error: function(xhr, status, error) {
                    $('#result').html('<p>Error: ' + error + '</p>');
                }
            });

            // show delete button if productCheckbox is cllcked
            $(document).on('change', '.productCheckBox', function() {
                if ($('.productCheckBox:checked').length > 0) {
                    $('#productDelete').show();
                    $('#productAddToSale').show();
                } else {
                    $('#productDelete').hide();
                    $('#productAddToSale').hide();
                }
            });

            // handle my delete using checkbox
            $('#productDelete').on('click', function() {
                var selectedIds = $('.productCheckBox:checked').map(function() {
                    return $(this).val();
                }).get();
                // console.log(selectedIds);
                if (selectedIds.length === 0) {
                    alert('No rows selected.');
                    return;
                }
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        const csrfToken = $('meta[name="csrf-token"]').attr('content');
                        $.ajax({
                            url: '/home/deleteProduct',
                            type: 'delete',
                            data: {
                                ids: selectedIds,
                            },
                            headers: {
                                'X-CSRF-TOKEN': csrfToken // Add CSRF token to headers
                            },
                            success: function(response) {
                                $('#productTable').DataTable().ajax.reload(null, false);
                                $('#productDelete').hide();
                                $('#productAddToSale').hide();
                                $('#ordersTable').DataTable().ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                // Handle error
                                alert('An error occurred while deleting rows.');
                            }
                        });
                        Swal.fire({
                            title: "Deleted!",
                            text: "Your file has been deleted.",
                            icon: "success"
                        });
                    }
                });

            });




        });
    </script>
@endsection
{{-- end of admin homepage --}}
