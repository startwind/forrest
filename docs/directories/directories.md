# Forrest Directories

Directories are an easy way to share commands and even collections of commands easily. This can be private within a
team, but also public with others.

## How to work with directories

A directory contains a list of repositories which then contain a list of commands. A user can activate a repository
after importing the directory and immediately use the commands.

Everybody can create its own directory by putting a YAML file on an place that is reachable for everybody who should be
able to use it. This can also be a private GitHub repository.

### Example of a directory file

```yaml
repositories:
  devops-my-team:
    adapter: yaml
    name: 'my-team-devops'
    description: 'Private devops command for the team'
    config:
      loader:
        type: github
        config:
          repository: forrest-directory
          user: leankoala-gmbh
          file: monitor.yml
          token: ghp_****
```

After creating such a `directory.yml` file you are able to register it. If it is brand new you have to add it by hand to
the `.forrest/config.yml` file in your home dir.

### `.forrest/config.yml` with registered directory.

If the directory is reachable via a public URL you can simply use the `url` parameter otherwise we recommend a loader.

```yaml
directories:
  forrest:
    url: 'https://raw.githubusercontent.com/startwind/forrest-directory/main/directory.yml'
  360monitoring:
    loader:
      type: github
      config:
        repository: forrest-directory
        user: leankoala-gmbh
        file: directory.yml
        token: github_****
```

If the configuration was successfully updated you can type `forrrest directory:list`. You should see now the new
repositories that are registered in the directory.

If you know want to share the directory with others use the `forrrest directory:export` command. It will create a command line prompt that will import it on other machines.

## Commands

- `directory:list` - List all directories including all connected repositories.
- `directory:import` - Import an existing directory.
- `directory:export` - Export an existing directory.
