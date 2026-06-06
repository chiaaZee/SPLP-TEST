<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ServiceCategory;
use Illuminate\Support\Str;

class ServiceCategoryTable extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    // Modal Form Data
    public $categoryId;
    public $name;
    public $slug;
    public $description;
    public $isEdit = false;

    protected $paginationTheme = 'bootstrap';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:service_categories,slug,' . $this->categoryId,
            'description' => 'nullable|string',
        ];
    }

    public function updatedName($value)
    {
        if (!$this->isEdit) {
            $this->slug = Str::slug($value);
        }
    }

    public function create()
    {
        $this->reset(['categoryId', 'name', 'slug', 'description', 'isEdit']);
        $this->dispatch('open-category-modal');
    }

    public function edit($id)
    {
        $category = ServiceCategory::findOrFail($id);
        $this->categoryId = $id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;
        $this->isEdit = true;

        $this->dispatch('open-category-modal');
    }

    public function store()
    {
        $this->validate();

        ServiceCategory::updateOrCreate(
            ['id' => $this->categoryId],
            [
                'name' => $this->name,
                'slug' => Str::slug($this->slug),
                'description' => $this->description
            ]
        );

        $this->dispatch('close-category-modal');
        $this->dispatch('swal:toast', type: 'success', message: 'Kategori berhasil disimpan.');
        $this->reset(['categoryId', 'name', 'slug', 'description', 'isEdit']);
    }

    public function delete($id)
    {
        ServiceCategory::find($id)?->delete();
        $this->dispatch('swal:toast', type: 'success', message: 'Kategori berhasil dihapus.');
    }

    public function render()
    {
        $categories = ServiceCategory::query()
            ->withCount('services')
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate($this->perPage);

        $totalCategories = ServiceCategory::count();
        $totalServices = \App\Models\ServiceCatalog::whereNotNull('category_id')->count();
        // Get category with most services
        $popularCategory = ServiceCategory::withCount('services')->orderBy('services_count', 'desc')->first();

        return view('livewire.admin.service-category-table', [
            'categories' => $categories,
            'totalCategories' => $totalCategories,
            'totalServices' => $totalServices,
            'popularCategory' => $popularCategory
        ]);
    }
}
