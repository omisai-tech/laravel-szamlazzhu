# Release process

Upon releasing a new version there's some checks and updates to be made:

- Clear your local repository with: `git add . && git reset --hard && git checkout master`
- Check the contents on https://github.com/omisai-tech/laravel-szamlazzhu/compare/{latest_version}...master and update the [changelog](CHANGELOG.md) file with the modifications on this release
> Note: make sure that there is no breaking changes and you may use `git tag --list` to check the latest release
- Commit the `src/SzamlaAgent.php` and `CHANGELOG.md` with the message: `git commit -m "Bump version to {new_version}"`
- `git push`
- `git tag {new_version}`
- `git push --tags`