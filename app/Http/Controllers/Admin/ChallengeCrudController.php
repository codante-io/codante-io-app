<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ChallengeRequest;
use App\Models\Track;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ChallengeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ChallengeCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Challenge::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/challenge');
        CRUD::setEntityNameStrings('challenge', 'challenges');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('status');
        CRUD::column('track_id');
        CRUD::column('track_position');
        CRUD::column('difficulty');
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ChallengeRequest::class);

        CRUD::field('name');
        CRUD::field('short_description')->label('Resumo');
        CRUD::field('description');
        CRUD::field('image_url')->type('url')->label('Link da Imagem');

        $this->crud->addField(
            [
                'name' => 'track_id',
                'label' => 'Track',
                'type' => 'select',
                'model' => 'App\Models\Track',
                'entity' => 'track',
                'attribute' => 'name',
            ]
        );

        CRUD::field('track_position')->type('number');
        CRUD::field('slug');

        $this->crud->addField(
            [
                'name'        => 'status',
                'label'       => 'Status',
                'type'        => 'radio',
                'options'     => [ 
                    0 => 'archived',
                    1 => 'draft',
                    2 => 'published',
                    3 => 'soon'
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
            ],
        );
        $this->crud->addField(
            [
                'name'        => 'difficulty',
                'label'       => 'Dificuldade (nível)',
                'type'        => 'radio',
                'options'     => [ 
                    0 => 1,
                    1 => 2,
                    2 => 3,
                ],
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
            ],
        );

        $this->crud->addField([
            'label' => 'Duração',
            'name' => 'duration_in_minutes',
            'type' => 'number',
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
        ]);

        $this->crud->addField([
            'label' => 'Repository URL',
            'name' => 'repository_url',
            'type' => 'url',
            'wrapper' => [
                'class' => 'form-group col-md-6',
            ],
        ]);

        CRUD::field('repository_url')->type('url');
        CRUD::field('published_at');
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}