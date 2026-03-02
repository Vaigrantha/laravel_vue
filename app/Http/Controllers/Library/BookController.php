<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use ZipArchive;

class BookController extends Controller
{
    public function previewMetadata(Request $request)
    {
        $validated = $request->validate([
            'epub' => 'required|file|mimes:epub|max:51200',
        ]);

        $epubFile = $validated['epub'];
        $temporaryPath = $epubFile->store('books/tmp', 'private');
        $absolutePath = Storage::disk('private')->path($temporaryPath);

        $epubData = $this->extractEpubData($absolutePath);
        $metadata = (array) ($epubData['metadata'] ?? []);

        $title = trim((string) ($metadata['title'] ?? ''));
        if ($title === '') {
            $title = pathinfo($epubFile->getClientOriginalName(), PATHINFO_FILENAME);
        }

        $author = trim((string) ($metadata['creator'] ?? ''));
        $description = trim((string) ($metadata['description'] ?? ''));
        [$existingAuthorId, $existingAuthorName] = $this->findExistingAuthor($author);

        return response()->json([
            'title' => $title !== '' ? $title : null,
            'author' => $existingAuthorName ?? ($author !== '' ? $author : null),
            'author_id' => $existingAuthorId,
            'description' => $description !== '' ? $description : null,
            'metadata' => $metadata,
        ]);
    }

    public function index()
    {
        return Inertia::render('Books/Index', [
            'books' => Book::query()->latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Books/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'epub' => 'required|file|mimes:epub|max:51200',
        ]);

        $epubFile = $request->file('epub');
        $storedPath = $epubFile->store('books/epub', 'private');
        $absolutePath = Storage::disk('private')->path($storedPath);

        $epubData = $this->extractEpubData($absolutePath);
        $metadata = (array) ($epubData['metadata'] ?? []);

        $resolvedTitle = trim((string) ($validated['title'] ?? ''));
        if ($resolvedTitle === '') {
            $resolvedTitle = trim((string) ($metadata['title'] ?? ''));
        }
        if ($resolvedTitle === '') {
            $resolvedTitle = pathinfo($epubFile->getClientOriginalName(), PATHINFO_FILENAME);
        }

        $resolvedDescription = trim((string) ($validated['description'] ?? ''));
        if ($resolvedDescription === '') {
            $resolvedDescription = trim((string) ($metadata['description'] ?? ''));
        }

        $resolvedAuthorName = trim((string) ($validated['author'] ?? ''));
        if ($resolvedAuthorName === '') {
            $resolvedAuthorName = trim((string) ($metadata['creator'] ?? ''));
        }

        [$authorId, $canonicalAuthorName] = $this->resolveOrCreateAuthor($resolvedAuthorName);
        if ($canonicalAuthorName !== null) {
            $resolvedAuthorName = $canonicalAuthorName;
        }

        Book::create([
            'title' => $resolvedTitle,
            'author' => $resolvedAuthorName !== '' ? $resolvedAuthorName : null,
            'author_id' => $authorId,
            'title_search' => $this->normalizeForSearch($resolvedTitle),
            'author_search' => $this->normalizeForSearch($resolvedAuthorName),
            'description' => $resolvedDescription !== '' ? $resolvedDescription : null,
            'file_path' => $storedPath,
            'cover_path' => null,
            'epub_data' => $epubData,
            'mime_type' => $epubFile->getClientMimeType(),
            'file_size' => $epubFile->getSize(),
        ]);

        return redirect()->route('admin.books.index');
    }

    public function edit(Book $book)
    {
        return Inertia::render('Books/Edit', [
            'book' => $book,
        ]);
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'epub' => 'nullable|file|mimes:epub|max:51200',
        ]);

        $existingMetadata = (array) (($book->epub_data ?? [])['metadata'] ?? []);

        $resolvedTitle = trim((string) ($validated['title'] ?? ''));
        if ($resolvedTitle === '') {
            $resolvedTitle = trim((string) ($existingMetadata['title'] ?? ''));
        }
        if ($resolvedTitle === '') {
            $resolvedTitle = $book->title;
        }

        $resolvedDescription = trim((string) ($validated['description'] ?? ''));
        if ($resolvedDescription === '') {
            $resolvedDescription = trim((string) ($existingMetadata['description'] ?? ''));
        }
        if ($resolvedDescription === '') {
            $resolvedDescription = (string) ($book->description ?? '');
        }

        $resolvedAuthorName = trim((string) ($validated['author'] ?? ''));
        if ($resolvedAuthorName === '') {
            $resolvedAuthorName = trim((string) ($existingMetadata['creator'] ?? ''));
        }
        if ($resolvedAuthorName === '') {
            $resolvedAuthorName = (string) ($book->author ?? '');
        }

        $updateData = [
            'title' => $resolvedTitle,
            'author' => $resolvedAuthorName !== '' ? $resolvedAuthorName : null,
            'title_search' => $this->normalizeForSearch($resolvedTitle),
            'author_search' => $this->normalizeForSearch($resolvedAuthorName),
            'description' => $resolvedDescription !== '' ? $resolvedDescription : null,
        ];

        if ($request->hasFile('epub')) {
            $epubFile = $request->file('epub');
            $storedPath = $epubFile->store('books/epub', 'private');
            $absolutePath = Storage::disk('private')->path($storedPath);
            $epubData = $this->extractEpubData($absolutePath);
            $metadata = (array) ($epubData['metadata'] ?? []);

            if (trim((string) ($validated['title'] ?? '')) === '' && ! empty($metadata['title'])) {
                $updateData['title'] = (string) $metadata['title'];
                $updateData['title_search'] = $this->normalizeForSearch((string) $metadata['title']);
            }

            if (trim((string) ($validated['description'] ?? '')) === '' && ! empty($metadata['description'])) {
                $updateData['description'] = (string) $metadata['description'];
            }

            if (trim((string) ($validated['author'] ?? '')) === '' && ! empty($metadata['creator'])) {
                $updateData['author'] = (string) $metadata['creator'];
                $updateData['author_search'] = $this->normalizeForSearch((string) $metadata['creator']);
            }

            $updateData['file_path'] = $storedPath;
            $updateData['epub_data'] = $epubData;
            $updateData['mime_type'] = $epubFile->getClientMimeType();
            $updateData['file_size'] = $epubFile->getSize();
        }

        [$authorId, $canonicalAuthorName] = $this->resolveOrCreateAuthor((string) ($updateData['author'] ?? ''));
        $updateData['author_id'] = $authorId;
        if ($canonicalAuthorName !== null) {
            $updateData['author'] = $canonicalAuthorName;
            $updateData['author_search'] = $this->normalizeForSearch($canonicalAuthorName);
        }

        $book->update($updateData);

        return redirect()->route('admin.books.index');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return back();
    }

    private function extractEpubData(string $absolutePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($absolutePath) !== true) {
            return [
                'error' => 'Unable to open EPUB archive.',
            ];
        }

        $entries = [];
        $documents = [];
        $metadata = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entryName = $zip->getNameIndex($i);
            if (! is_string($entryName)) {
                continue;
            }

            $stat = $zip->statIndex($i);
            $entrySize = is_array($stat) ? ($stat['size'] ?? 0) : 0;

            $entries[] = [
                'path' => $entryName,
                'size' => $entrySize,
            ];

            $extension = strtolower(pathinfo($entryName, PATHINFO_EXTENSION));
            if (! in_array($extension, ['xhtml', 'html', 'xml', 'opf', 'ncx', 'txt', 'css'], true)) {
                continue;
            }

            $content = $zip->getFromIndex($i);
            if (! is_string($content)) {
                continue;
            }

            $documents[] = [
                'path' => $entryName,
                'content' => $content,
            ];

            if ($extension === 'opf' && $metadata === []) {
                $metadata = $this->extractMetadataFromOpf($content);
            }
        }

        $zip->close();

        return [
            'metadata' => $metadata,
            'entries' => $entries,
            'documents' => $documents,
        ];
    }

    private function extractMetadataFromOpf(string $opfContent): array
    {
        $metadata = [];

        $xml = @simplexml_load_string($opfContent);
        if ($xml === false) {
            return $metadata;
        }

        $namespaces = $xml->getNamespaces(true);
        $dcUri = $namespaces['dc'] ?? null;

        if ($dcUri === null) {
            return $metadata;
        }

        $dc = $xml->children($dcUri);

        $metadata['title'] = isset($dc->title) ? trim((string) $dc->title) : null;
        $metadata['creator'] = isset($dc->creator) ? trim((string) $dc->creator) : null;
        $metadata['language'] = isset($dc->language) ? trim((string) $dc->language) : null;
        $metadata['identifier'] = isset($dc->identifier) ? trim((string) $dc->identifier) : null;
        $metadata['publisher'] = isset($dc->publisher) ? trim((string) $dc->publisher) : null;
        $metadata['description'] = isset($dc->description) ? trim((string) $dc->description) : null;

        return array_filter($metadata, static fn ($value) => $value !== null && $value !== '');
    }

    private function normalizeForSearch(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->value();

        return $normalized !== '' ? $normalized : null;
    }

    /**
     * @return array{0: int|null, 1: string|null}
     */
    private function resolveOrCreateAuthor(?string $authorName): array
    {
        $authorName = trim((string) $authorName);
        if ($authorName === '') {
            return [null, null];
        }

        [$existingAuthorId, $existingAuthorName] = $this->findExistingAuthor($authorName);
        if ($existingAuthorId !== null && $existingAuthorName !== null) {
            return [$existingAuthorId, $existingAuthorName];
        }

        return $this->createAuthor($authorName);
    }

    /**
     * @return array{0: int|null, 1: string|null}
     */
    private function findExistingAuthor(?string $authorName): array
    {
        $authorName = trim((string) $authorName);
        if ($authorName === '') {
            return [null, null];
        }

        $normalizedTarget = $this->normalizeForSearch($authorName);
        if ($normalizedTarget === null) {
            return [null, null];
        }

        $authors = User::query()
            ->role('author')
            ->select(['id', 'name', 'email'])
            ->get();

        foreach ($authors as $author) {
            if ($this->normalizeForSearch($author->name) === $normalizedTarget) {
                return [$author->id, $author->name];
            }
        }

        $targetToken = explode(' ', $normalizedTarget)[0] ?? null;
        if ($targetToken !== null && $targetToken !== '') {
            foreach ($authors as $author) {
                $normalizedExisting = (string) ($this->normalizeForSearch($author->name) ?? '');
                if ($normalizedExisting !== '' && str_contains($normalizedExisting, $targetToken)) {
                    return [$author->id, $author->name];
                }
            }
        }

        return [null, null];
    }

    /**
     * @return array{0: int|null, 1: string|null}
     */
    private function createAuthor(string $authorName): array
    {
        $baseEmail = Str::slug($authorName, '.');
        if ($baseEmail === '') {
            $baseEmail = 'author';
        }

        $candidate = $baseEmail;
        $counter = 1;
        while (User::query()->where('email', $candidate.'@authors.local')->exists()) {
            $counter++;
            $candidate = $baseEmail.$counter;
        }

        $author = User::create([
            'name' => $authorName,
            'email' => $candidate.'@authors.local',
            'password' => Hash::make(Str::random(32)),
        ]);
        $author->assignRole('author');

        return [$author->id, $author->name];
    }
}
