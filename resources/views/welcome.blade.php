@extends('layouts.app')

@section('content')
    <div class="container-lg">
        <div id="toolbar">
            <button id="remove" class="btn btn-danger mb-3" disabled>
                Удалить
            </button>
        </div>
        <table class="table table-hover table-bordered" id="tableSelect">
            <thead class="table-dark">
            <tr>
                <th class="select-cell">Выбор</th>
                <th>Пользователь</th>
                <th>Отдел</th>
                <th>Должность</th>
                <th class="change-cell">Изменение</th>
            </tr>
            </thead>
            <tbody id="table_body">
            </tbody>
        </table>

        <button id="add" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">Добавить</button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                <button class="btn btn-primary" type="submit" id="btn_save" value="1">Сохранить</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .select-cell, .change-cell {
            width: 50px;
        }
    </style>

    {{--  Table data  --}}
    <script>
        let $table_body = $('#table_body')
        let $table_row = $('#table_row')

        let $departments = [
            {id: 1, name: 'Отдел_1'},
            {id: 2, name: 'Отдел_2'},
            {id: 3, name: 'Отдел_3'},
            {id: 4, name: 'Отдел_4'},
        ]
        let $positions = [
            {id: 1, name: 'Должность_1'},
            {id: 2, name: 'Должность_2'},
        ]
        let $positions_hook = [
            {id: 1, position: 1, department: [1, 2, 4]},
            {id: 1, position: 2, department: [3, 4]},
        ]
        let $table_data = [
            {id: 1, name: 'Пользователь_1', department: [1, 2], position: 1},
            {id: 2, name: 'Пользователь_2', department: [3, 4], position: 2},
            {id: 3, name: 'Пользователь_3', department: [1, 2, 4], position: 1},
        ]


        function TableDataParser(value) {
            let department = []
            let position = $positions.find(item => item.id === value.position).name

            $.each(value.department, function (i, val) {
                department[i] = $departments.find(item => item.id === val).name
            })

            return {id: value.id, name: value.name, department: department, position: position}
        }

        function TableRowAdd(data) {
            let block = "<tr id='table_row_" +
                data.id + "'> " +
                "<td class='align-middle text-center' id='cb_block'> " +
                "<label for='cb'></label><input type='checkbox' id='cb' value='" +
                data.id + "'/> </td> " +
                "<td class='align-middle' id='row_name'>" +
                data.name + "</td> " +
                "<td class='align-middle' id='row_department'>" + data.department.join('<br/>') + "</td> " +
                "<td class='align-middle' id='row_position'>" +
                data.position + "</td> " +
                "<td class='align-middle text-center'> " +
                "<button id='change' class='btn btn-outline-primary rounded-circle' data-bs-toggle='modal' data-bs-target='#exampleModal'> " +
                "<i class='mdi mdi-pencil mdi-18px'></i> " +
                "</button> </td> </tr>"

            $table_body.append(block)
        }

        $.each($table_data, function (index, value) {
            let data = TableDataParser(value)

            TableRowAdd(data)
        });
    </script>

    {{--  Form  --}}
    <script>
        let $modal_form = $('#modal_form')
        let $form_user = $('#form_user')
        let $form_position = $('#form_position')
        let $form_departments = $('#form_departments')
        let $btn_save = $('#btn_save')

        $.each($table_data, function (index, value) {
            $form_user.append($('<option>', {
                id: 'form_user_option_' + value.id,
                value: value.id,
                text: value.name
            }));
        });

        $.each($positions, function (index, value) {
            $form_position.append($('<option>', {
                id: 'form_position_option_' + value.id,
                value: value.id,
                text: value.name
            }));
        });

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

        $modal_form.submit((e) => {
            e.preventDefault()
        })

        function RowAdd(data) {

        }

        function RowChange() {

        }

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

            let data = {id: 10, name: Number(user.val()), department: depart_array, position: Number(position.val())}

            if (Number($btn_save.val()))
                RowChange(data)
            else
                RowAdd(data)

            $('#modal_close').click()
        })

        function ClearForm() {
            let modal = $('#exampleModal')
            let user = modal.find('#form_user')
            let departments = modal.find('#form_departments')
            let position = modal.find('#form_position')

            user.find('option[id*="form_user_option_"]:selected').prop('selected', false)
            departments.find('input[id*="form_cb_"]:checked').prop("checked", false);
            position.find('option[id*="form_position_option_"]:selected').prop('selected', false)
        }
    </script>

    {{--  Table function  --}}
    <script>
        let $table = $('#tableSelect')
        let $tableRow = $table.find('tr')
        let $remove = $('#remove')
        let $change = $tableRow.find('td #change')
        let $add = $('#add')
        let clickCb = false
        let clickChange = false

        $change.click(function () {
            let data_id = Number($(this).parent().parent().find('#cb').val())
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

            user.find('option[value="' + data_hook.id + '"]').prop('selected', true)

            $.each(data_hook.department, function (index, value) {
                departments.find('#form_cb_' + value).prop('checked', true)
            })

            position.find('option[value="' + data_hook.position + '"]').prop('selected', true)

            clickChange = true
        })

        $add.click(() => {
            let modal = $('#exampleModal')
            let title = modal.find('#exampleModalLabel')
            let user = modal.find('#form_user')

            ClearForm()

            title.text('Добавление')
            user.attr('disabled', false)
            $btn_save.text('Добавить')
            $btn_save.val(0)
        })

        $('input:checkbox').click(function () {
            clickCb = true
        })

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

        $remove.click(function () {
            let selects = $table.find('td input:checkbox')

            selects.each(function (index, checkbox) {
                if (checkbox.checked) {
                    $table.find('#table_row_' + checkbox.value).remove()
                }
            });
        })
    </script>
@endsection
