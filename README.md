# QUBESHub Instance of The HUBzero® Platform for Scientific Collaboration

**Main idea**:  The `app` directory is a subtree of both `dev` and the `master` branch, pointing to the `app` branch on GitHub.  The `dev` machine has `origin` set to `hubzero/hubzero-cms`, while the vagrant box has `origin` set to `qubeshub/hubzero-cms`.

# Development Workflow

The vagrant box pulls from this fork of the HubZero CMS.  So, if you are developing within the `app` directory and do not care about pushing changes to `dev` yet, you should be able to simply give the following:

```
git push origin master
```

Interesting stuff happens only when you want code pushed to (or from) `dev`.

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
