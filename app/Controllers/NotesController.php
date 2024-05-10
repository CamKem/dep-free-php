<?php

namespace App\Controllers;

use App\Core\Controller;
use app\Core\Database\Database;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Validator;
use App\Core\View;

class NotesController extends Controller
{

    protected Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = app(Database::class);
    }

    public function index(): View
    {
        $notes = $this->db->query('select * from Notes where user_id = 1')->get();
        return view("notes.index", [
            'heading' => 'My Notes',
            'notes' => $notes
        ]);
    }

    public function create(): View
    {
        return view("notes.create", [
            'heading' => 'Create Note',
            'errors' => []
        ]);
    }

    public function store(): View|Response
    {
        $errors = [];

        if (! Validator::string($_POST['body'], 1, 1000)) {
            $errors['body'] = 'A body of no more than 1,000 characters is required.';
        }

        if (! empty($errors)) {
            return view("notes.create", [
                'heading' => 'Create Note',
                'errors' => $errors
            ]);
        }

        $this->db->query('INSERT INTO notes(body, user_id) VALUES(:body, :user_id)', [
            'body' => $_POST['body'],
            'user_id' => 1
        ]);

        return redirect()->route('notes.show', ['note' => $this->db->lastInsertId()]);

    }

    public function show(Request $request): View
    {
        $currentUserId = 1;

        $note = $this->db->query('select * from notes where id = :id', [
            'id' => $request->get('note')
        ])->findOrFail();

        authorize($note['user_id'] === $currentUserId);

        return view("notes.show", [
            'heading' => 'Note',
            'note' => $note
        ]);

    }

    public function edit(): View
    {
        $currentUserId = 1;

        $note = $this->db->query('select * from notes where id = :id', [
            'id' => $_GET['id'] ?? null
        ])->findOrFail();

        authorize($note['user_id'] === $currentUserId);

        return view("notes.edit", [
            'heading' => 'Edit Note',
            'note' => $note,
            'errors' => []
        ]);
    }

    public function update(): View|Response
    {
        $errors = [];

        if (! Validator::string($_POST['body'], 1, 1000)) {
            $errors['body'] = 'A body of no more than 1,000 characters is required.';
        }

        if (! empty($errors)) {
            return view("notes.edit", [
                'heading' => 'Edit Note',
                'note' => [
                    'id' => $_POST['id'],
                    'body' => $_POST['body']
                ],
                'errors' => $errors
            ]);
        }

        $this->db->query('update notes set body = :body where id = :id', [
            'body' => $_POST['body'],
            'id' => $_POST['id']
        ]);

        return redirect()->route('notes.show', [
            'id' => request()->get('id')
        ]);
    }

    public function destroy(): Response
    {

        // TODO:: set up a flash message to confirm the delete

        $currentUserId = 1;

        $id = request()->get('id');

        $note = $this->db->query('select * from notes where id = :id', compact('id'))
            ->findOrFail();

        authorize($note['user_id'] === $currentUserId);

        $this->db->query('delete from notes where id = :id', compact('id'));

        return redirect()->route('notes.index');
    }

}

