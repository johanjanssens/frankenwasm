---
name: test-docker
description: Build and smoke test the FrankenWASM Docker image against all demo pages
user_invocable: true
---

# Test FrankenWASM Docker Image

Build the Docker image, start a container, and verify all demo pages return HTTP 200.

## Steps

1. Build the image:
```bash
GITHUB_TOKEN=$(gh auth token) docker build --secret id=github_token,env=GITHUB_TOKEN -t frankenwasm .
```
If `gh auth token` fails, build without the secret (slower):
```bash
docker build -t frankenwasm .
```

2. Start the container:
```bash
docker rm -f frankenwasm-test 2>/dev/null
docker run --rm -d --name frankenwasm-test -p 8080:8080 frankenwasm
```

3. Wait for startup (all 20 plugins must compile), then verify the server log shows `Starting FrankenWASM server`:
```bash
sleep 15
docker logs frankenwasm-test 2>&1 | tail -2
```

4. Test ALL demo pages return HTTP 200. Run each curl and report pass/fail:

| URL | Description |
|-----|-------------|
| `http://localhost:8080/` | Landing page |
| `http://localhost:8080/ascii/` | ASCII art |
| `http://localhost:8080/blurhash/` | Blur hash |
| `http://localhost:8080/diagrams/` | Diagrams |
| `http://localhost:8080/enhance-ssr/` | Custom element SSR |
| `http://localhost:8080/highlight/` | Syntax highlighting |
| `http://localhost:8080/html-rewriter/` | HTML transformations |
| `http://localhost:8080/jsonpath/` | JSONPath queries |
| `http://localhost:8080/jsx-include/` | JSX SSR |
| `http://localhost:8080/katex/` | LaTeX math |
| `http://localhost:8080/langdetect/` | Language detection |
| `http://localhost:8080/markdown/` | Markdown |
| `http://localhost:8080/minify/` | Minification |
| `http://localhost:8080/qrcode/` | QR codes |
| `http://localhost:8080/sanitize/` | HTML sanitization |
| `http://localhost:8080/scss/` | SCSS compilation |
| `http://localhost:8080/toml/` | TOML conversion |

Use a loop:
```bash
FAILED=0
for path in / /ascii/ /blurhash/ /diagrams/ /enhance-ssr/ /highlight/ /html-rewriter/ /jsonpath/ /jsx-include/ /katex/ /langdetect/ /markdown/ /minify/ /qrcode/ /sanitize/ /scss/ /toml/; do
  STATUS=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080${path}")
  if [ "$STATUS" = "200" ]; then
    echo "PASS $path"
  else
    echo "FAIL $path (HTTP $STATUS)"
    FAILED=$((FAILED + 1))
  fi
done
echo ""
if [ "$FAILED" -eq 0 ]; then echo "All pages passed"; else echo "$FAILED page(s) failed"; fi
```

5. Check logs for panics or crashes:
```bash
docker logs frankenwasm-test 2>&1 | grep -i "panic\|crash\|signal\|SIGBUS" | head -5
```

6. Tear down:
```bash
docker rm -f frankenwasm-test
```

7. Report results: image size, number of plugins loaded, pages passed/failed, any errors.
