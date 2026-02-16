<?
namespace App\Http\Controllers\Library;

use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view books')->only('index');
        $this->middleware('permission:create books')->only(['create','store']);
        $this->middleware('permission:edit books')->only(['edit','update']);
        $this->middleware('permission:delete books')->only('destroy');
    }

    public function index()
    {
        return Inertia::render('Books/Index', [
            'books' => Book::latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Books/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'author' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        Book::create($validated);

        return redirect()->route('books.index');
    }

    public function edit(Book $book)
    {
        return Inertia::render('Books/Edit', [
            'book' => $book
        ]);
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'author' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $book->update($validated);

        return redirect()->route('books.index');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return back();
    }
}
