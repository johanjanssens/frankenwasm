<?php use FrankenPHP\Wasm; ?>
<?php
$_title = 'Syntax Highlighting';
$_prev = ['url' => '../markdown/', 'label' => 'Markdown'];
$_next = ['url' => '../jsonpath/', 'label' => 'JSONPath'];
include __DIR__ . '/../_header.php';
?>

    <div class="intro">
        <div class="intro-badges">
            <span class="badge badge-go">Go</span>
            <?php if (isset($_sizes['chroma'])): ?><span class="badge badge-size"><?= _fmtSize($_sizes['chroma']) ?></span><?php endif; ?>
        </div>
        <h1>Syntax Highlighting</h1>
        <p>Code highlighting via Chroma: <code>$chroma->call('transform', ['code' => ..., 'lang' => ...])</code></p>
    </div>

    <?php $chroma = new Wasm('chroma'); ?>

    <?php
    // PHP example
    $phpCode = <<<'EOD'
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('active', true)
                          ->orderBy('created_at', 'desc')
                          ->take(10)
                          ->get();

        return view('products.index', compact('products'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return view('products.show', [
            'product' => $product,
            'related' => $product->related()->take(3)->get()
        ]);
    }
}
EOD;

    $start = microtime(true);
    $phpResult = $chroma->call('transform', ['code' => $phpCode, 'lang' => 'php']);
    $phpTime = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #8892BF">
        <div class="example-header">
            <h2>PHP</h2>
            <span class="timing"><?= number_format($phpTime, 2) ?> ms</span>
        </div>
        <div class="example-body"><?= $phpResult ?></div>
    </div>

    <?php
    // Go example
    $goCode = <<<'EOD'
package main

import (
    "fmt"
    "net/http"
)

func main() {
    http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
        fmt.Fprintf(w, "Hello, %s!", r.URL.Path[1:])
    })

    fmt.Println("Server starting on :8080")
    http.ListenAndServe(":8080", nil)
}
EOD;

    $start = microtime(true);
    $goResult = $chroma->call('transform', ['code' => $goCode, 'lang' => 'go']);
    $goTime = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #00ADD8">
        <div class="example-header">
            <h2>Go</h2>
            <span class="timing"><?= number_format($goTime, 2) ?> ms</span>
        </div>
        <div class="example-body"><?= $goResult ?></div>
    </div>

    <?php
    // JavaScript example
    $jsCode = <<<'EOD'
import express from 'express';

const app = express();
const port = 3000;

app.get('/api/users', async (req, res) => {
    const users = await db.query('SELECT * FROM users');
    res.json({ data: users, count: users.length });
});

app.listen(port, () => {
    console.log(`Server running on port ${port}`);
});
EOD;

    $start = microtime(true);
    $jsResult = $chroma->call('transform', ['code' => $jsCode, 'lang' => 'javascript']);
    $jsTime = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #F7DF1E">
        <div class="example-header">
            <h2>JavaScript</h2>
            <span class="timing"><?= number_format($jsTime, 2) ?> ms</span>
        </div>
        <div class="example-body"><?= $jsResult ?></div>
    </div>

    <?php
    // Rust example
    $rustCode = <<<'EOD'
use actix_web::{web, App, HttpServer, HttpResponse};

#[actix_web::main]
async fn main() -> std::io::Result<()> {
    HttpServer::new(|| {
        App::new()
            .route("/", web::get().to(index))
            .route("/users/{id}", web::get().to(get_user))
    })
    .bind("127.0.0.1:8080")?
    .run()
    .await
}

async fn index() -> HttpResponse {
    HttpResponse::Ok().body("Hello, World!")
}
EOD;

    $start = microtime(true);
    $rustResult = $chroma->call('transform', ['code' => $rustCode, 'lang' => 'rust']);
    $rustTime = (microtime(true) - $start) * 1000;
    ?>

    <div class="example" style="border-top: 4px solid #DEA584">
        <div class="example-header">
            <h2>Rust</h2>
            <span class="timing"><?= number_format($rustTime, 2) ?> ms</span>
        </div>
        <div class="example-body"><?= $rustResult ?></div>
    </div>

<?php include __DIR__ . '/../_footer.php'; ?>
