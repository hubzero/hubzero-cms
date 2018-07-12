# QUBESHub Instance of The HUBzero® Platform for Scientific Collaboration

All extensions of the `app` directory are subtrees (effectively).

## Components

 * [com_fmns](https://github.com/qubeshub/com_fmns): Faculty Mentoring Networks component (_in development_). **dev** :x: **prod** :x:
 * [com_partners](https://github.com/qubeshub/com_partners): Partners component **dev** :white_check_mark: **prod** :white_check_mark:
 * [com_publications](https://github.com/qubeshub/com_publications): **Override** of HubZero publications component. **dev** :white_check_mark: **prod** :white_check_mark:
 * [com_tags](https://github.com/qubeshub/com_tags): **Override** of HubZero tags component. **dev** :white_check_mark: **prod** :white_check_mark:

## Modules

Note:  Needed to include extra installation directory instructions in the `composer.json` files so that they install in the `mod_` prefix directories.

 * [mod_partners](https://github.com/qubeshub/mod_partners): Shows partners in grid format. **dev** :white_check_mark: **prod** :white_check_mark:
 * [mod_qubes_events](https://github.com/qubeshub/mod_qubes_events): Displays QUBES Happening events, consisting mainly of faculty mentoring networks. **dev** :white_check_mark: **prod** :white_check_mark:
 * [mod_qubes_tools](https://github.com/qubeshub/mod_qubes_tools): Displays QUBES Tools in grid format (_in development_). **dev** :x: **prod** :x:
 * [mod_showcase](https://github.com/qubeshub/mod_showcase): Displays static and dynamic billboards in grid format. **dev** :white_check_mark: **prod** :white_check_mark:
 * [mod_slider](https://github.com/qubeshub/mod_slider): Displays billboards in slider format (_not live on QUBES_) **dev** :white_check_mark: **prod** :white_check_mark:

## Plugins
 * [plg_content_qubesmacros](https://github.com/qubeshub/plg_content_qubesmacros): Content plugin that contains QUBES macros. **dev** :white_check_mark: **prod** :white_check_mark:
 * [plg_projects_publications](https://github.com/qubeshub/plg_projects_publications): **Override** of HubZero project publications plugin. **dev** :white_check_mark: **prod** :white_check_mark:
 * [plg_groups_publications](https://github.com/qubeshub/plg_groups_publications): Publication plugin for groups component. **dev** :white_check_mark: **prod** :x:
 * [plg_system_menurouter](https://github.com/qubeshub/plg_system_menurouter): Prepend menu parent items to generated component URLs and route menu items appropriately. **dev** :white_check_mark: **prod** :white_check_mark:
 * [plg_system_subnav](https://github.com/qubeshub/plg_system_subnav): Component/URL to subnavigation mapping **dev** :white_check_mark: **prod** :white_check_mark:

## Templates

 * [tpl_bmc](https://github.com/qubeshub/tpl_bmc): QUBES 2018 template **dev** :white_check_mark: **prod** :white_check_mark:
 * [tpl_qubes](https://github.com/qubeshub/tpl_qubes): QUBES 2015 template **dev** :white_check_mark: **prod** :white_check_mark:

# Development Workflow

The vagrant box pulls from this fork of the HubZero CMS.  So, if you are developing within the `app` directory and do not care about updating the servers (`dev`, `stage`, `qa`, or `prod`), you should be able to simply give the following:

```
git push origin master
```

Make sure before you push, though, to pull in changes first:

```
git pull --rebase origin master
```

Interesting stuff happens only when you want to update the code on the servers.

Note that in the commands below, while we are referring to "subtrees", we are not using the "subtree" scripts bundled with git.  This is due to (1) the complete clusterfudge that occurs with history when using subtrees, and (2) the occurrence of the [subtree-cache directory](https://github.com/dflydev/git-subsplit/issues/14) which gets HUGE and is never cleaned.  Also, going the manual route forces a deeper understanding of git, which overall HAS to be a good thing, right?

**Reference for manual subtree commands**:  [Mastering Git subtrees](https://medium.com/@porteneuve/mastering-git-subtrees-943d29a798ec) by [Christophe Porteneuve](https://medium.com/@porteneuve?source=post_header_lockup).

## Adding remote extension as a subtree to this repository

First, add the remote repository and fetch the repo:

```
[master]$ git remote add <extension> https://github.com/qubeshub/<extension>
[master]$ git fetch <extension>
```

Now, pull in the extension repository and put it into the correct subdirectory of `app`.

```
[master]$ git read-tree --prefix=app/<extension dir> -u <extension>/master
```

`<extension dir>` can be
 * `app/components/<extension>`,
 * `app/modules/<extension>`,
 * `app/plugins/<extension>`,
 * `app/templates/<extension>`

The previous `read-tree` command will not commit the result - we'll have to do that ourselves.

```
[master]$ git commit -m "Added <extension> as subtree"
```

## Updating remote extensions

This repository is designed so you can code within multiple extensions at once, pushing and pulling from `origin`, without having to worry about updating the remote extensions, _unless you want to update the code on the servers_.  This section covers how to do just that.  We'll assume we want to update an extension called `ext_foo`.

For the code that follows, the `[main]$` prompt tells us we are on the main repo (this one), while the `[foo]$` prompt tells us we are on the master branch of `ext_foo`.

### Preliminary:  Make sure you have this extension setup as a remote:

```
[main]$ git remote
```

If you don't see it in the list, add it:

```
[main]$ git remote add ext_foo https://github.com/qubeshub/ext_foo.git
```

### Fetch remote data

First, `fetch` all remote changes.

```
[main]$ git fetch --all
```

You could only pull in the repo you want to update, which would just be

```
[main]$ git fetch <package>
```

### Get list of commits you want to backport to the package

This command will show all commits that touched a specific directory where our package resides (`app/ext/foo`).

```
[main]$ git log --oneline -- app/ext/foo
```

Grab the hashes for the commits of interest.

### Create a branch that tracks the master branch of `ext_foo`

```
[main]$ git checkout -b ext_foo ext_foo/master
```

### Begin to cherry-pick your commits

There are a few commands that we should be aware of:

 1. `git cherry-pick -x <hash>`
 2. `git cherry-pick -x --no-commit <hash>`
 3. `git cherry-pick -x --strategy=subtree <hash>`

It is a good idea to look at each commit to see if there was an edit, addition, or deletion, as the addition and deletions will cause some trouble.

#### Only files were added in a commit

In this case, you need to use option #2 above.  Git doesn't know about these files yet in the package repo, and so it will add it to the package repo in the subdirectory structure of the main repo **_and then will automatically commit_**.  We need to manually tell git where the files should go:

```
[foo]$ git cherry-pick -x --no-commit <hash>
[foo]$ git status
...
Changes to be committed:
  (use "git reset HEAD <file>..." to unstage)

	new file:   app/ext/foo/<new file #1>
...
```

All we need to do now is a `git mv` for all new files

```
[foo]$ git mv app/ext/foo/<new file #1> <new file #1>
```

Finally, copy-and-paste the commit message from the main repository

```
[foo]$ git commit -m <commit message>
```

**Note**:  I'm curious of option #3 would help here, i.e. if it does a subtree merge, will it know to strip the `app/ext/foo`?  See next section note as well.

#### Only files were deleted in a commit

You might get the following:

```
[foo]$ git cherry-pick -x <hash>
error: could not apply <hash>... [Commit message here]
hint: after resolving the conflicts, mark the corrected paths
hint: with 'git add <paths>' or 'git rm <paths>'
hint: and commit the result with 'git commit'
```

```
On branch ext_foo
Your branch is ahead of 'ext_foo/master' by xx commits.
  (use "git push" to publish your local commits)

You are currently cherry-picking commit <hash>.
  (fix conflicts and run "git cherry-pick --continue")
  (use "git cherry-pick --abort" to cancel the cherry-pick operation)

Unmerged paths:
  (use "git add <file>..." to mark resolution)

	added by us:     <deleted file #1>
	added by us:     <deleted file #2>
```

Just `git rm` each file separately and then type `git cherry-pick --continue`.  

**Note**:  I'm pretty sure we can give strategy options here, such as `-Xtheirs`.  My impression here is that git sees that the extension has the files, but the main repo does not, and it doesn't know which one to respect.  By saying `theirs` we are telling git which one to do.

#### Modifications seen as deletion by ours?

Sometimes a commit with only edits comes across as a deletion.  For example,

```
You are currently cherry-picking commit <hash>.
  (fix conflicts and run "git cherry-pick --continue")
  (use "git cherry-pick --abort" to cancel the cherry-pick operation)

Changes to be committed:

	modified:   css/pages/resourceportal.css
	modified:   css/pages/resourceportal.scss

Unmerged paths:
  (use "git add/rm <file>..." as appropriate to mark resolution)

	deleted by us:   app/templates/bmc/css/pages/variables.scss
```

First, `mv` the file, as the `deleted by us` version is the correct version:

```
mv app/templates/bmc/css/pages/variables.scss css/pages/variables.scss
```

Note that `git mv` won't work as the `deleted by us` file is not under version control.  Now if you do `git status`, you might seen

```
...
Unmerged paths:
  (use "git add/rm <file>..." as appropriate to mark resolution)

	deleted by us:   app/templates/bmc/css/pages/variables.scss

Changes not staged for commit:
  (use "git add <file>..." to update what will be committed)
  (use "git checkout -- <file>..." to discard changes in working directory)

	modified:   css/pages/variables.scss
...
```

Strangely, we have to `git rm` the `app/templates/bmc` file and `git add` the modified file for staging.

```
[foo]$ git rm app/templates/bmc/css/pages/variables.scss
[foo]$ git add css/pages/variables.scss
```

We can then continue the cherry-picking
```
[foo]$ git cherry-pick --continue
```

### Push to remote after all cherry-picks

```
[foo]$ git push ext_foo HEAD:master
```

## How to push changes to `dev`

If you want to push changes to `dev`, you need to update the `app` repository, as the `dev` machine has the `app` directory added as a subtree.  To do this:

```
git subtree push --prefix=app origin app
```

This will update the `app` branch on GitHub, as long as `origin` is set to `https://github.com/qubeshub/hubzero.cms` (which it should be).

On the `dev` machine, all you have to do now is:

```
git subtree pull --prefix=app qubeshub app
```

Note the change of the remote from `origin` to `qubeshub`.  On `dev`, `origin` is set to `https://github.com/hubzero/hubzero.cms`, and `qubeshub` is set to `https://github.com/qubeshub/hubzero.cms`.

# How to push changes from `dev`

If you are developing on `dev`, then NEVER push to `origin` (well, you probably won't be able to anyways).  In this case, everything should be subtree pushed, like this:

```
git subtree push --prefix=app qubeshub app
```

Then, in order for those changes to be integrated into the `master` branch, perform the following from the vagrant box:

```
git subtree pull --prefix=app origin app
```

Note that this is essentially the reverse workflow of working on the vagrant box.

# Monthly updates

On the Thursday or Friday before QA push, do the following:

```
=======
git fetch --all
git pull upstream 2.2
```

This will pull in the new code into `/core`.  For the extensions that have overrides, perform the following in order (they may or may not work).

## 1. Update app

You first want to make sure your local `app` branch is up-to-date.  From the local `master` branch:

```
// Pushes app to GitHub
git subtree push --prefix=app origin app

// Switch to app branch
git checkout app

// Pull in changes from GitHub
git pull --rebase origin app
```

For the extensions in question, go to `hubzero/hubzero-cms` and see what changes were made.  Then, grab the commit of interest and perform a cherry pick:

```
git cherry-pick <commit hash>
```

This may or may not work!  If you run into trouble, try the next thing.

## 2. Subtree a subtree?

From the `app` branch, perform the following:

```
git subtree split -P <extension> -b <extension>
git checkout <extension>
git cherry-pick <commit hash>
```

If this works, then you can merge the changes back into the `app` branch:

```
git checkout app
git merge -Xsubtree=<extension> <extension>
```

If THIS works, then you want to update the `app` branch on GitHub and pull in the changes to `master`:

```
git push origin app
git checkout master
git subtree pull --prefix=app origin app
```

## 3. Template overrides

Be careful, as there are cases where we have overrides of php files in `app/templates/bmc/html`.  In this case, do manual checks.  Using FileMerge can be helpful here.

# Fixing core issues

Checkout a clone of `upstream/2.2`:

```
git checkout --track -b fix-core-bug upstream/2.2
```

Code the fix and then push the new branch to origin:

```
git push -u origin fix-core-bug
```

Then, do a pull request!  If you don't want to keep the local and/or remote branch:

```
git branch -D fix-core-bug // Delete locally
git push -d origin fix-core-bug // Delete remotely (I would wait to delete the remote branch until AFTER pull has occurred)
```
