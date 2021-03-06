<?php
namespace App\DataTables;

use App\Content;
use App\ContentType;
use Buttom;
use FlashHelper;
use Yajra\DataTables\DataTables;
use function redirect;

class ContentDatatable extends Content
{
    protected $table = 'contents';

    /**
     * Build DataTable class.
     *
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($type_slug = null)
    {
        ## Columnas adicionales.
        $columns = $this->getColumns();

        $datatable = DataTables::of($this->getQuery($type_slug));

        ## Añado todas las columnas adicioneles al objeto Datatable
        foreach ($columns as $idx => $column) {
            $datatable->addColumn($idx, function ($ele) use ($column) {
                return $this->{$column}($ele);
            });
        }

        return $datatable->make();
    }

    /**
     * Realiza la consulta a la db.
     * @param string $type El tipo de contenido, será null para devolver todos.
     *
     * @return mixed
     */
    protected function getQuery($type_slug) {
        $contents = Content::whereNull('deleted_at');

        ## En caso de recibir slug filtro ese tipo de contenido, sino todos.
        if ($type_slug) {
            $type = ContentType::where('slug', $type_slug)->first();

            if (!$type) {
                $type = new ContentType([
                    'name' => 'Todos',
                    'slug' => 'all',
                    'icon' => 'fa fa-file',
                ]);
            }

            $contents->where('type_id', $type->id);
        } else {
            $type = new ContentType([
                'name' => 'Todos',
                'slug' => 'all',
                'icon' => 'fa fa-file',
            ]);
        }

        ## Ordeno resultados.
        $contents->orderBy('status_id', 'ASC')
            ->orderBy('created_at','DESC')
            ->with(['status', 'user', 'type'])
            ->get();
        return $contents;
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html()
    {
      /*
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->orderBy(1)
            ->buttons(
                Button::make('create'),
                Button::make('export'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            );
      */
    }

    /**
     * Devuelve un array con las columnas adicionales para la tabla.
     *
     * @return array Devuelve un array con clave el campo y valor el nombre de
     *               la función que lo generará.
     */
    protected function getColumns()
    {
        return [
            'action' => 'columnActions',
        ];
    }

    protected function columnActions($ele)
    {
        return Buttom::view(
                $ele->url,
                $ele->id
            ) .
            Buttom::edit(
                $ele->urlEdit,
                $ele->id
            ) .
            Buttom::delete(
                '#',
                $ele->id
            );
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return $this->type->name . date('YmdHis');
    }
}
