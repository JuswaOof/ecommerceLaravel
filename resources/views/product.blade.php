@section('product')                
{{-- start of admins product section --}}
<div class="row px-5 py-2 d-flex flex-grow-1 border-bottom">
    <p class="d-flex justify-content-end">bell icon here</p>
</div>

<div class="row px-5 py-4 flex-grow-1 justify-content-center align-items-center border-bottom">
    <div class="input-group mb-3 d-flex flex-column">
        <div>
            <p>Home > Products</p>
        </div>
        <div>
            <p>Total Products</p>
        </div>
        <div class="d-flex justify-content-between">
            <div>
                <div>
                    <input class="glass p-2" type="date" name="" id="">
                </div>
            </div>
            <div>
                <button class="btn glass text-white" data-bs-toggle="modal"
                    data-bs-target="#addProduct">Add Product</button>
                <button class="btn glass text-white" data-bs-toggle="modal"
                    data-bs-target="#addToSale">Add to Sale</button>
            </div>
        </div>
    </div>
</div>

<div class="row my-5 px-5 py-2 d-flex flex-grow-1 overflow-auto">
    <table id="productTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th scope="col"><input type="checkbox"></th>
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
                    <input name="productName" type="text" class="form-control" id="productName"
                        aria-describedby="emailHelp">
                </div>
                <div class="mb-3">
                    <label for="productDescription" class="form-label">Product Description</label>
                    <input name="productDescription" type="text" class="form-control"
                        id="productDescription">
                </div>
                <div class="mb-3 pb-1">
                    <label for="price" class="form-label">Price</label>
                    <input name="price" type="text" class="form-control" id="price">
                </div>
                <div class="mb-3 border-bottom pb-3">
                    <input name="imageUpload" type="file" class="form-control" id="imageUpload"
                        accept="image/*">
                    <div class="d-flex justify-content-center py-3">
                        <img id="imagePreview" src="" alt="Image Preview"
                            style="display: none; max-width: 100px; height: 100px;">
                    </div>
                </div>
                <div class="mb-3 form-check py-3 d-flex justify-content-around">
                    <select name="isFeatured" class="form-select mx-2"
                        aria-label="Default select example">
                        <option value="Featured">Featured</option>
                        <option value="Not Featured">Not Featured</option>
                    </select>

                    <select name="isAvailable" class="form-select mx-2"
                        aria-label="Default select example">
                        <option value="Available">Available</option>
                        <option value="Not Available">Not Available</option>
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
<div class="modal fade" id="editProductBtn" tabindex="-1" aria-labelledby="editProductBtn"
aria-hidden="true">
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
                    <input name="editproductName" id="editProductName" type="text"
                        class="form-control" id="productName" aria-describedby="emailHelp">
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
                <div class="mb-3 border-bottom pb-3">
                    <input name="editImageUpload" id="editImageUpload" type="file"
                        class="form-control" accept="image/*">
                    <div class="d-flex justify-content-center py-3">
                        <img id="editImagePreview" src="" alt="Image Preview"
                            style="display: none; max-width: 100px; height: 100px;">
                    </div>
                </div>
                <div class="mb-3 form-check py-3 d-flex justify-content-around">
                    <select name="editIsFeatured" id="editIsFeatured" class="form-select mx-2"
                        aria-label="Default select example">
                        <option value="Featured">Featured</option>
                        <option value="Not Featured">Not Featured</option>
                    </select>

                    <select name="editIsAvailable" id="editIsAvailable" class="form-select mx-2"
                        aria-label="Default select example">
                        <option value="Available">Available</option>
                        <option value="Not Available">Not Available</option>
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
@endsection