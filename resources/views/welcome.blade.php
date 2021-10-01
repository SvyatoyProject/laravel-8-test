@extends('layouts.app')

@section('content')
    <div class="container-lg">
        <div id="toolbar">
            <button id="remove" class="btn btn-danger mb-3 text-light"
                    disabled {{ !auth()->user() || auth()->user()->rights > 1 ? 'hidden' : '' }}>
                Удалить
            </button>
        </div>
        @guest
            <div class="align-middle text-center">
                <h1>ДОБРО ПОЖАЛОВАТЬ!</h1>
                <h3><a href="{{ route('login') }}">Войдите</a>/<a href="{{ route('register') }}">Зарегистрируйтесь</a>
                    для просмотра таблицы пользователей</h3>
            </div>
        @else
            <table class="table table-hover table-bordered" id="tableSelect">
                <thead class="table-dark">
                <tr>
                    <th class="select-cell" {{ !auth()->user() || auth()->user()->rights > 1 ? 'hidden' : '' }}>Выбор
                    </th>
                    <th>Пользователь</th>
                    <th>Отдел</th>
                    <th>Должность</th>
                    <th class="change-cell" {{ !auth()->user() || auth()->user()->rights > 2 ? 'hidden' : '' }}>
                        Изменение
                    </th>
                </tr>
                </thead>
                <tbody id="table_body"></tbody>
            </table>

            <button id="add" class="btn btn-success text-light" data-bs-toggle="modal"
                    data-bs-target="#exampleModal" {{ !auth()->user() || auth()->user()->rights > 2 ? 'hidden' : '' }}>
                Добавить
            </button>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Редактирование</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="modal_form">
                                <div>
                                    <label class="form-label" for="form_user"></label>
                                    <select class="form-select" id="form_user" required>
                                        <option selected value="">Выбор пользователя</option>
                                    </select>
                                </div>
                                <div class="list-group overflow-auto mt-3" id="form_departments"
                                     style="height: 120px"></div>
                                <div>
                                    <label class="form-label" for="form_position"></label>
                                    <select class="form-select" id="form_position">
                                        <option selected value="">Выбор должности</option>
                                    </select>
                                </div>
                                <div class="mt-3">
                                    <button type="button" class="btn btn-secondary" id="modal_close"
                                            data-bs-dismiss="modal">Закрыть
                                    </button>
                                    <button class="btn btn-primary text-light" type="submit" id="btn_save" value="1">Сохранить
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endguest
    </div>

    <style>
        .select-cell, .change-cell {
            width: 50px;
        }
    </style>

    <script>
        let $table_body = $('#table_body')
        let $table_row = $('#table_row')

        // Получение данных для таблицы (axios)
        axios.get('get-users').then(res => {
            //
            {{--  Table  --}}
            //

            let $table_data = res.data['users']
            let $positions = res.data['positions']
            let $departments = res.data['departments']

            // Цикл: Заполнение таблицы
            $.each($table_data, function (index, value) {
                if (!value.table_data) return;
                let data = TableDataParser(value)

                TableRowAdd(data)
            });

            // Функция присвоения значений таблице
            function TableDataParser(value) {
                let department = []
                let position = ''
                try {
                    position = $positions.find(item => item.id === value.position).name
                } catch (e) {
                }


                $.each(value.department, function (i, val) {
                    department[i] = $departments.find(item => item.id === val).name
                })

                return {id: value.id, name: value.name, department: department, position: position}
            }

            // Функция добавления строки в таблицу
            function TableRowAdd(data) {
                let block = "<tr id='table_row_" +
                    data.id + "'> " +
                    "<td class='align-middle text-center' id='cb_block' {{ !auth()->user() || auth()->user()->rights > 1 ? 'hidden' : '' }}> " +
                    "<label for='cb_" + data.id + "'></label><input type='checkbox' id='cb_" + data.id + "' value='" +
                    data.id + "'/> </td> " +
                    "<td class='align-middle' id='row_name'>" +
                    data.name + "</td> " +
                    "<td class='align-middle' id='row_department'>" + data.department.join('<br/>') + "</td> " +
                    "<td class='align-middle' id='row_position'>" +
                    data.position + "</td> " +
                    "<td class='align-middle text-center' {{ !auth()->user() || auth()->user()->rights > 2 ? 'hidden' : '' }}> " +
                    "<button id='change' class='btn btn-outline-primary rounded-circle' data-bs-toggle='modal' data-bs-target='#exampleModal'> " +
                    "<i class='mdi mdi-pencil mdi-18px'></i> " +
                    "</button> </td> </tr>"

                $table_body.append(block)
            }

            //
            {{--  Table function  --}}
            //

            let $table = $('#tableSelect')
            let $tableRow = $table.find('#table_body tr')
            let $remove = $('#remove')
            let $change = $tableRow.find('td #change')
            let $add = $('#add')
            let clickCb = false
            let clickChange = false

            // Событие нажатия кнопки изменения
            $change.click(function () {
                let data_id = Number($(this).parent().parent().find('input:checkbox[id*="cb"]').val())
                let data_hook = $table_data.find(item => item.id === data_id)

                let modal = $('#exampleModal')
                let title = modal.find('#exampleModalLabel')
                let user = modal.find('#form_user')
                let departments = modal.find('#form_departments')
                let position = modal.find('#form_position')

                title.text('Редактирование')
                user.attr('disabled', true)
                $btn_save.text('Сохранить')
                $btn_save.val(1)

                ClearForm()
                FormUserData(false)

                user.find('option[value="' + data_hook.id + '"]').prop('selected', true)

                $.each(data_hook.department, function (index, value) {
                    departments.find('#form_cb_' + value).prop('checked', true)
                })

                position.find('option[value="' + data_hook.position + '"]').prop('selected', true)

                clickChange = true
            })

            // Событие нажатия кнопки добавления
            $add.click(() => {
                let modal = $('#exampleModal')
                let title = modal.find('#exampleModalLabel')
                let user = modal.find('#form_user')

                ClearForm()

                title.text('Добавление')
                user.attr('disabled', false)
                $btn_save.text('Добавить')
                $btn_save.val(0)

                FormUserData()
            })

            // Событие нажатия checkbox в таблице
            $('input:checkbox').click(function () {
                clickCb = true
            })

            // Событие нажатия строки в таблице
            $tableRow.click(function () {
                if (clickChange) {
                    return clickChange = false
                }

                let cell = $(this).find('td')
                let select = cell.find('input:checkbox')[0]
                let selects = $table.find('td input:checkbox')
                let check = false

                if (!clickCb)
                    select.checked = !select.checked
                else
                    clickCb = false

                if (select.checked)
                    cell.addClass('table-info')
                else
                    cell.removeClass('table-info')

                selects.each(function (index, checkbox) {
                    if (checkbox.checked) {
                        return check = true
                    }
                });

                $remove.prop('disabled', !check)
            })

            // Событие нажатия кнопки удаления
            $remove.click(function () {
                let selects = $table.find('td input:checkbox:checked')
                let ids = []

                selects.each((index, select) => {
                    ids[index] = Number(select.value)
                })

                axios.post('delete-user', {
                    id: ids
                }).then(result => {
                    console.log(result)
                    location.reload()
                }).catch((e) => {
                    console.log(e)
                    alert('Ошибка: ' + e)
                })
            })

            //
            {{--  Form  --}}
            //

            let $modal_form = $('#modal_form')
            let $form_user = $('#form_user')
            let $form_position = $('#form_position')
            let $form_departments = $('#form_departments')
            let $btn_save = $('#btn_save')

            // Цикл: Заполение списка должностей
            $.each($positions, function (index, value) {
                $form_position.append($('<option>', {
                    id: 'form_position_option_' + value.id,
                    value: value.id,
                    text: value.name
                }));
            });

            // Цикл: Заполение списка отделов
            $.each($departments, function (index, value) {
                let label = $('<label class="list-group-item">').attr('for', "form_cb_" + value.id)
                let input = $('<input type="checkbox" class="form-check-input me-1">').attr({
                    id: 'form_cb_' + value.id,
                    value: value.id
                });

                input.appendTo(label)
                label.append(value.name)
                $form_departments.append(label)
            })

            // Событие нажатия кнопки сохранения/изменения
            $btn_save.click(() => {
                let modal = $('#exampleModal')
                let user = modal.find('#form_user option:selected')
                let departments = modal.find('#form_departments input:checkbox[id*="form_cb_"]:checked')
                let depart_array = []
                let position = modal.find('#form_position option:selected')

                $.each(departments, function (index, value) {
                    depart_array[index] = Number(value.value)
                })

                if (!user.val()) return;

                let data = {
                    id: Number(user.val()),
                    department: depart_array,
                    position: Number(position.val())
                }

                if (Number($btn_save.val()))
                    RowChange(data)
                else
                    RowAdd(data)

                $('#modal_close').click()
            })

            // Функция добавления записи в таблицу (axios)
            function RowAdd(data) {
                axios.post('add-user', data).then(result => {
                    console.log(result)
                }).catch((e) => {
                    console.log(e)
                    alert('Ошибка: ' + e)
                })
            }

            // Функция изменения записи в таблице (axios)
            function RowChange(data) {
                axios.post('update-user', data).then(result => {
                    console.log(result)
                }).catch((e) => {
                    console.log(e)
                    alert('Ошибка: ' + e)
                })
            }

            // Функция заполения списка пользователей
            function FormUserData($add = true) {
                $.each($table_data, function (index, value) {
                    if ($add && value.table_data) return;
                    $form_user.append($('<option>', {
                        id: 'form_user_option_' + value.id,
                        value: value.id,
                        text: value.name
                    }));
                });
            }

            // Функция очистки формы
            function ClearForm() {
                let modal = $('#exampleModal')
                let user = modal.find('#form_user')
                let departments = modal.find('#form_departments')
                let position = modal.find('#form_position')

                user.find('option[id*="form_user_option_"]').remove()
                departments.find('input[id*="form_cb_"]:checked').prop("checked", false);
                position.find('option[id*="form_position_option_"]:selected').prop('selected', false)
            }
        }).catch(err => {
            let block = "<tr><td class='align-middle text-center table-danger fw-bold' colspan='5'>Ошибка получения данных!</td></tr>"

            $table_body.append(block)
            console.log(err)
        })
    </script>
@endsection
