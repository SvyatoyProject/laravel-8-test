@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-3">
                <div class="card">
                    <img
                        src="{{ file_exists(public_path(auth()->user()->image))
                                    && auth()->user()->image
                                    ? auth()->user()->image
                                    : 'images/default-user-profile.png' }}"
                        class="card-img-top" id="profile_image" alt="Фото профиля">
                    <div class="card-body">
                        <input type="file" class="form-control" id="select_image"
                               aria-describedby="upload_image" aria-label="Upload" accept="image/*">
                    </div>
                </div>
            </div>
            <div class="col">
                <h2>{{ auth()->user()->name }}</h2>
                <h3 id="user_right"></h3>
                <hr/>
                <h4>{{ auth()->user()->email }}</h4>
                <h4>Дата создания: {{ date('d.m.Y', strtotime(auth()->user()->created_at)) }}</h4>
                <h4>В таблице пользователей: {{ auth()->user()->table_data ? 'Да' : 'Нет' }}</h4>
                <hr/>
                <h5 id="right_description"></h5>
            </div>
        </div>
    </div>

    <script>
        $user_right = $('#user_right')
        $right_description = $('#right_description')

        axios.get('/profile-data').then(res => {
            let rights = res.data['rights']

            $user_right.text(rights[0].name)
            $right_description.text(rights[0].description)
        }).catch(e => {
            console.log(e)
        })
    </script>

    <script>
        let $select_image = $('#select_image')

        $select_image.change(function () {
            readURL(this);
        })

        function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();

                reader.onload = function (e) {
                    $('#profile_image').attr('src', e.target.result);
                    axios.post('/upload-image', {
                        type: input.files[0].name.split('.').pop().toLowerCase(),
                        image: e.target.result
                    }).then(res => {
                        console.log(res)
                    }).catch(e => {
                        console.log(e)
                    })
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
