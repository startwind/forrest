# Forrest - CLI tool to manage and run your common command line calls.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/startwind/forrest/badges/quality-score.png?b=main&s=579fbe045436392cced69014e18889609b7d2b1a)](https://scrutinizer-ci.com/g/startwind/forrest/?branch=main)

Often there are only a handful of commands that are used in everyday life, also many command line tools have a lot of possible options, but only a few of them are used. We have designed Forrest to store exactly these commands centrally and thus simplify the use of the command line in a much.

![commands:show](docs/images/commands_list.png)

## Installation

Download the latest version of our PHAR archive and give it afterwards rights to be executed.

```shell
wget https://github.com/startwind/forrest/releases/latest/download/forrest.phar
chmod +x forrest.phar
```

## Usage

The default installation of Forrest comes with only a few commands. We decided to keep the basic version very clean. Nevertheless there is the official Forrest directory where a lot of predefined commands are located. To list all the repositories run 

```shell
./forrest.phar directory:list
```

Choose the repositories you want to use. Behind every of those there can be many commands. 

## Commands

### Repository Commands

- `repository:list` - Shows a list of all registered repositories.

### Commands

- `commands:list` - List all registered commands that can be run.
- `commands:show` - Show a single command with all its details and steps that will be executed. 
- `commands:run` - Run a single command.

### Directory Commands

- `directory:list` - List all repositories from the Forrest directory.
- `directory:install` - Install a specified repository from the Forrest directory.

## Why we have chosen Forrest as the name

It's an homage to one our favorit movies "Forrest Gump" and especially the scene with "Run Forrest! Run!". And we run as well. Command line scripts.

## How to add your custom repository
