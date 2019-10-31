<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAddRequest;
use App\Role;
use App\SocialNetwork;
use App\User;
use App\UserData;
use App\UserDetail;
use App\UserSocial;
use Buttom;
use Carbon\Carbon;
use Exception;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use LogHelper;
use RoleHelper;
use Yajra\DataTables\DataTables;
use function auth;
use function compact;
use function config;
use function dd;
use function is_null;
use function isEmpty;
use function redirect;
use function response;
use function route;
use function url;
use function view;

class UserController extends Controller
{
    /**
     * Añade un nuevo usuario, solo si tiene permisos para ello.
     */
    public function add($user_id = null)
    {
        if (!RoleHelper::canUserCreate()) {
            return redirect()->back()->with([
                'error' => 'No tiene permisos para crear usuarios',
            ]);
        }

        $socialNetworks = SocialNetwork::all();
        $roles = null;

        ## Si es admin o superadmin muestra los roles que puede asignar.
        if (RoleHelper::isSuperAdmin()) {
            ## Es superadmin, puede asignar todos los roles.
            $roles = Role::all();
        } else if (RoleHelper::isAdmin()) {
            ## Es admin, puede asignar todos menos otros admin/superadmin
            $roles = Role::all()->whereNotIn('id', [1]);
        }

        $user = User::find($user_id);
        $user_data = $user ? $user->data : null;
        $user_detail = $user ? $user->details : null;
        $user_social = $user ? $user->social : null;
        return view('panel.users.edit')->with([
            'socialNetworks' => $socialNetworks,
            'user_id' => $user_id,
            'user' => $user,
            'user_data' => $user_data,
            'user_detail' => $user_detail,
            'user_social' => $user_social,
            'roles' => $roles,
        ]);
    }

    /**
     * Modifica o crea un nuevo usuario.
     *
     * @param null                              $id
     * @param \App\Http\Requests\UserAddRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(UserAddRequest $request, $user_id = null)
    {
        if ($user_id) {
            $permission = RoleHelper::canUserEdit($user_id);
        } else {
            $permission = RoleHelper::canUserCreate();
        }

        if (!$permission) {
            return redirect()->back()->with([
                'error' => 'No tiene permisos para editar o crear el usuario',
            ]);
        }

        $action = 'create'; ## Identifica si se crea o edita (create|edit)

        ## Registro de Log.
        LogHelper::register('info', 'Creado usuario');

        /**
         * Busco usuario, lo crea en caso de no existir.
         */
        $user = User::find($user_id);

        if ($user) {
            $action = 'edit';
            $userDataModel = UserData::where('id', $user->data_id)->first();
            $userDetailModel = UserDetail::where('id', $user->detail_id)->first();
        } else {
            $user = new User(['role_id' => 3]);
            $userDataModel = new UserData();
            $userDetailModel = new UserDetail();
        }

        ## Almaceno los datos de usuario.
        $userData = UserData::addEdit($userDataModel, $request);

        ## Almaceno detalles del usuario.
        $userDetail = UserDetail::addEdit($userDetailModel, $request);

        ## Guardo y almaceno el usuario.
        $user->fill([
            'data_id' => $userData->id,
            'detail_id' => $userDetail->id,
            'name' => $request->get('name'),
            'nick' => $request->get('nick'),
            'email' => $request->get('email'),
        ]);

        ## Compruebo la contraseña antes de asignarla al usuario.
        $password = $request->get('password');
        if (isset($password) && !empty($password) && (mb_strlen($password) >= 6)) {
            $user->password = Hash::make($password, ['rounds' => config('constant.bcrypt_cost')]);
        }

        $role_id = $request->get('role_id') ?? null;

        ## Compruebo que puede asignar el rol recibido.
        if (RoleHelper::isSuperAdmin() && $role_id) {
            $user->role_id = $role_id;
        } elseif (RoleHelper::isAdmin() && $role_id && ($role_id != 1)) {
            $user->role_id = $role_id;
        }

        $user->save();

        ## Almaceno redes sociales.
        $social_id = $request->get('social_id') ?? null;
        $social_nick = $request->get('social_nick') ?? null;
        $social_url = $request->get('social_url') ?? null;

        $socialNetworks = UserSocial::saveAllForUser(
            compact('social_id', 'social_nick', 'social_url', 'user_id'),
            $user->id
        );

        return redirect()->route('panel.users.view', ['id' => $user->id])->with([
        ]);
    }

    /**
     * Vista individual de usuario.
     */
    public function view($id = null)
    {
        $user_id = $id ?: auth()->id();

        if (!RoleHelper::canUserView($user_id)) {
            return redirect()->back()->with([
                'error' => 'No tiene permisos para crear usuarios',
            ]);
        }

        ## Obtengo el usuario (Así no repite consultas al ver mi propio user)
        if ((int)$id === (int)auth()->id()) {
            $user = auth()->user();
        } else {
            $user = User::find($user_id);
        }

        if (!$user) {
            return redirect()->back()->with([
                'error' => 'Hubo un problema mientras se buscaba al usuario.'
            ]);
        }

        return view('panel.users.view')->with([
            'user_id' => $user_id,
            'user' => $user,
            'nick' => $user->nick,
        ]);
    }

    /**
     * Procesa el borrado de un usuario.
     *
     * @param null $user_id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($user_id = null)
    {
        $user = User::find($user_id);
        $actual_user = (int)$user_id === (int)auth()->id();

        if ($user_id && RoleHelper::canUserDelete($user_id)) {
            $user->delete();

            return redirect()->back()->with([
                'error' => 'Se ha eliminado el usuario correctamente.'
            ]);
        }

        ## Si el usuario que se borra es el actual, se cierra su sesión.
        if ($actual_user === true) {
            auth()->logout();
        }

        return redirect()->back()->with([
            'error' => 'No tienes permisos.'
        ]);
    }

    /**
     * Muestra la vista principal de usuarios con el listado de todos.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        ## Usuarios Activos que según el role del actual puede ver.
        if (RoleHelper::isSuperAdmin()) {
            $users = User::allActive();
        } else if (RoleHelper::isAdmin()) {
            $users = User::allActive()->whereNotIn('role_id', [1]);
        } else {
            $users = User::allActive()->whereNotIn('role_id', [1, 2]);
        }

        $n_users = $users->count();

        $n_users_this_month = $users->whereBetween('created_at',
            [
                Carbon::now()->subMonth()->format('Y-m-d H:i:s'),
                Carbon::now()->format('Y-m-d H:i:s'),
            ]
        )->count();

        ## Usuarios Inactivos (SoftDelete)
        $usersInactive = User::allInactive();
        $n_usersInactive = $usersInactive->count();

        return view('panel.users.index')->with([
            //'users' => $users,
            'n_users' => $n_users,
            'n_users_this_month' => $n_users_this_month,
            //'usersInactive' => $usersInactive,
            'n_usersInactive' => $n_usersInactive,
        ]);
    }

    /**
     * Modifica si el usuario está activo o inactivo (Soft-delete).
     */
    public function toggleActiveAjax($user_id)
    {
        $user = User::find($user_id);

        if ($user) {
            $user->deleted_at = $user->deleted_at ? null : Carbon::now();

            return response()->json([
                'message' => $user->deleted_at ? 'Usuario Desactivado' : 'Usuario Activado',
                'text_buttom' => $user->deleted_at ? 'Activar' : 'Desactivar'
            ], 201);
        }

        return response()->json([
            'message' => 'Usuario no encontrado',
        ], 404);
    }

    /****************** DATATABLES ******************/

    /**
     * Función genérica para mostrar datatable de usuarios.
     *
     * @param $users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function createUserDatatable($users)
    {
        try {
            return DataTables::of($users)
                ->addColumn('action', function ($user) {
                    $buttons =
                        Buttom::view(
                            route('panel.users.view', ['$id' => $user->id]),
                            $user->id
                        ) .

                        Buttom::edit(
                            route('panel.users.add', ['user_id' => $user->id]),
                            $user->id
                        ) .

                        Buttom::delete(
                            route('panel.users.delete', ['user_id' => $user->id]),
                            $user->id
                        );

                    if (RoleHelper::isSuperAdmin()) {
                        $buttons .= Buttom::action(url('#'), $user->id, [
                            'text' => $user->deleted_at ? 'Activar' : 'Desactivar',
                            'icon' => 'fa fa-tasks',
                        ]);
                    }

                    return $buttons;
                })
                ->editColumn('created_at', function ($user) {
                    if (is_null($user->created_at)) {
                        return 'N/D';
                    }
                    return $user->created_at->format('d/m/Y H:m:i');
                })
                ->make(true);
        } catch (Exception $e) {
            return response()->json('FALLO Datatable');
        }
    }

    /**
     * Devuelve los datos para Datatable con todos los usuarios en la
     * aplicación.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTableAllUser()
    {
        $users = User::all();
        return $this->createUserDatatable($users);
    }

    /**
     * Devuelve los nuevos usuarios creados en el último mes.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTableThisMontUsers()
    {
        $users = User::all()->whereBetween('created_at',
            [
                Carbon::now()->subMonth()->format('Y-m-d H:i:s'),
                Carbon::now()->format('Y-m-d H:i:s'),
            ]
        );

        return $this->createUserDatatable($users);
    }

    /**
     * Devuelve todos los usuarios inactivos en el sistema.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTableInactiveUsers()
    {
        if (!RoleHelper::isAdmin()) {
            return response()->json(['Error' => 'No Tienes permisos'], 403);
        }

        $users = User::allInactive();

        return $this->createUserDatatable($users);
    }

    /**
     * Devuelve todos los usuarios bloqueados en el sistema.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTableBlockedUsers()
    {
        //TODO → completar función para bloquear usuarios por fallos de login
        // y también manualmente.

        if (!RoleHelper::isAdmin()) {
            return response()->json(['Error' => 'No Tienes permisos'], 403);
        }

        //$users = User::allBlocked();

        //return $this->createUserDatatable($users);
    }
}
