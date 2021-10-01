@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-3">
                <div class="card">
                    <img
                        src="public/images/default-user-profile.png"
                        class="card-img-top" alt="Фото профиля">
                    <div class="card-body">
                        <input type="file" class="form-control" id="selectImage"
                                   aria-describedby="uploadImage" aria-label="Upload">
                        <div class="d-grid mt-3">
                            <button class="btn btn-outline-primary d-grid" type="button" id="uploadImage" disabled>
                                Загрузить
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col">
                <div class="bg-light">Custom column padding</div>
            </div>
        </div>
    </div>

    <script>
        let $upload_image = $('#uploadImage')
    </script>
@endsection
