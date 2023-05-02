# The YAML repository format

## Example

```yaml
commands:
  
  startServer:
    name: "server:start"
    description: "Starting the built-in web server"
    prompt: "php -S localhost:8000"
    
  memoryLimit:
    name: "run:memory:limit"
    description: "Run a php CLI command with a defined memory limit"
    prompt: "php -d memory_limit=${limit_in_megabyte}M ${filename}"
```
