<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableController
{
    function GetTableData(): array
    {
        // Получение таблицы пользователей
        $users = User::select(['id', 'name', DB::raw('null AS department'), 'position', 'table_data'])
            ->get();

        // Получение отделов/должностей
        $departments = DB::table('departments')->get();
        $positions = DB::table('positions')->get();

        // Попытка присвоения отделов пользователям
        try {
            $department_hook = DB::table('department_hook')->get();

            foreach ($users as $user) {
                $department_user = $department_hook->filter(function ($item) use ($user) {
                    return $item->user == $user->id;
                });

                $depart_arr = [];
                foreach ($department_user as $depart) {
                    $depart_arr[] = $depart->department;
                }

                $user->department = $depart_arr;
            }
        } catch (QueryException $ex) {}

        return ['users' => $users, 'departments' => $departments, 'positions' => $positions];
    }

    function AddTableData(Request $request): bool
    {
        $user = auth()->user();
        if (!$user->rights || $user->rights > 2) return false;

        try {
            // Добавление в таблицу пользователей
            User::where('id', $request->id)
                ->update([
                    'position' => $request->position ?: null,
                    'table_data' => 1
                ]);

            if (count($request->department) <= 0) return true;

            // Добавление привязок к отделам
            $department = null;
            foreach ($request->department as $depart) {
                $department[] = [
                    'user' => $request->id,
                    'department' => $depart
                ];
            }

            DB::table('department_hook')->insert($department);
        } catch (QueryException $ex) {
            return false;
        }

        return true;
    }

    function UpdateTableData(Request $request): bool
    {
        $user = auth()->user();
        if (!$user->rights || $user->rights > 2) return false;

        try {
            // Обновление таблицы пользователей
            User::where('id', $request->id)
                ->update([
                    'position' => $request->position ?: null
                ]);

            // Удаление старых привязок к отделам
            DB::table('department_hook')->where('user', $request->id)->delete();

            if (count($request->department) <= 0) return true;

            // Добавление новых привязок к отделам
            $department = null;
            foreach ($request->department as $depart) {
                $department[] = [
                    'user' => $request->id,
                    'department' => $depart
                ];
            }

            DB::table('department_hook')->insert($department);
        } catch (QueryException $ex) {
            return false;
        }

        return true;
    }

    function DeleteTableData(Request $request): bool
    {
        $user = auth()->user();
        if (!$user->rights || $user->rights > 1) return false;

        try {
            // Удаление из таблицы пользователей
            User::whereIn('id', $request->id)
                ->update([
                    'position' => null,
                    'table_data' => null
                ]);

            // Удаление привязок к отделам
            DB::table('department_hook')->whereIn('user', $request->id)->delete();
        } catch (QueryException $ex) {
            return false;
        }

        return true;
    }
}
