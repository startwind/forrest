# Functions

Functions are an easy way to enrich commands with dynamic data. 

> **_NOTE:_**  If you are missing a function for your prompts feel free to create an issue and we will try to implement it. 

## `date()`

The date function injects the current date in any needed format. As format we have chosen the [PHP date format](https://www.php.net/manual/en/datetime.format.php).

### Example

This prompt will echo the current date in the `YYYY-MM-DD` format (e.g. 2025-01-20). 

```yaml
prompt: echo ${date(Y-m-d)}
```
