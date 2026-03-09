<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BookModel;
use App\Services\BookService;

class Books extends BaseController
{
    protected $bookModel;
    protected $service;

    public function __construct()
    {
        $this->bookModel = new BookModel();
        $this->service   = new BookService();
    }

    /*
    |--------------------------------------------------------------------------
    | LIST
    |--------------------------------------------------------------------------
    */
    public function index()
    {

        $status = $this->request->getGet('status');

        $builder = $this->bookModel;

        if ($status === 'active') {
            $builder->where('is_active', 1);
        } elseif ($status === 'archived') {
            $builder->where('is_active', 0);
        }

        $data['books'] = $builder->orderBy('created_at', 'DESC')->paginate(10);
        $data['pager'] = $this->bookModel->pager;

        return view('books/index', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('books/form');
    }

    public function store()
    {
        try {
            $this->service->create(
                $this->request->getPost(),
                session('user_id')
            );

            return redirect()->to('/books')
                ->with('success', 'Buku berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $data['book'] = $this->bookModel->find($id);

        if (!$data['book']) {
            return redirect()->to('/books')
                ->with('error', 'Buku tidak ditemukan');
        }

        return view('books/form', $data);
    }

    public function update($id)
    {
        try {
            $this->service->update(
                $id,
                $this->request->getPost(),
                session('user_id')
            );

            return redirect()->to('/books')
                ->with('success', 'Buku berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function delete($id)
    {
        try {
            $this->service->delete($id, session('user_id'));

            return redirect()->to('/books')
                ->with('success', 'Buku berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('/books')
                ->with('error', $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ARCHIVE dan RESTORE
    |--------------------------------------------------------------------------
    */
    public function archive($id)
    {
        try {
            $this->service->archive($id, session('user_id'));

            return redirect()->to('/books')
                ->with('success', 'Buku berhasil diarsipkan');
        } catch (\Exception $e) {
            return redirect()->to('/books')
                ->with('error', $e->getMessage());
        }
    }

    public function restore($id)
    {
        try {
            $this->service->restore($id, session('user_id'));

            return redirect()->to('/books')
                ->with('success', 'Buku berhasil diaktifkan kembali');
        } catch (\Exception $e) {
            return redirect()->to('/books')
                ->with('error', $e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | View List Buku Member
    |--------------------------------------------------------------------------
    */

    public function listMember()
    {
        $data['books'] = $this->bookModel
            ->where('is_active', 1)
            ->orderBy('judul', 'ASC')
            ->paginate(10);

        $data['pager'] = $this->bookModel->pager;

        return view('books/member_list', $data);
    }
}

