# Commit Guidelines

This project follows the [Conventional Commits](https://conventionalcommits.org/) specification for commit messages. Conventional Commits provide a standardized format that makes it easier to understand the nature of changes, automate versioning, and generate changelogs.

## Format

Commit messages must follow this structure:

```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

### Type

The `type` field is mandatory and describes the kind of change introduced by the commit. Common types include:

- **feat**: A new feature
- **fix**: A bug fix
- **docs**: Documentation changes
- **style**: Changes that do not affect the meaning of the code (white-space, formatting, missing semi-colons, etc.)
- **refactor**: A code change that neither fixes a bug nor adds a feature
- **test**: Adding missing tests or correcting existing tests
- **chore**: Changes to the build process or auxiliary tools and libraries (e.g., documentation generation)
- **perf**: A code change that improves performance
- **ci**: Changes to CI configuration files and scripts
- **build**: Changes that affect the build system or external dependencies
- **revert**: Reverts a previous commit

### Scope (Optional)

The `scope` provides additional contextual information and is contained within parentheses. For example, `feat(auth): add login functionality`.

### Description

The `description` is a short summary of the change, written in imperative mood (e.g., "add" instead of "added"). It should be concise and not exceed 72 characters.

### Body (Optional)

The `body` provides additional details about the change. It should explain what and why the change was made, not how.

### Footer (Optional)

The `footer` is used for breaking changes or referencing issues. For breaking changes, start with `BREAKING CHANGE:` followed by a description.

Examples of footers:
- `BREAKING CHANGE: remove support for Node 6`
- `Closes #123`

## Examples

- `feat: add user authentication`
- `fix(ui): resolve button alignment issue`
- `docs: update README with installation instructions`
- `refactor(api): simplify user model`
- `test: add unit tests for login function`

## Breaking Changes

If a commit introduces a breaking change, it must include `BREAKING CHANGE:` in the footer. The description should explain what broke and how to migrate.

## Why Conventional Commits?

- **Automated Versioning**: Tools like semantic-release can automatically determine version bumps (major, minor, patch) based on commit types.
- **Clear History**: Makes the commit history more readable and searchable.
- **Changelog Generation**: Facilitates automatic generation of changelogs.

## Tools and Enforcement

- Use tools like `commitizen` or `commitlint` to enforce these guidelines.
- Ensure your commit messages are validated in CI/CD pipelines.

For more details, refer to the [Conventional Commits specification](https://conventionalcommits.org/).