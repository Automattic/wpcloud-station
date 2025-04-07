#!/bin/bash

# Script to lint only JavaScript files that have changed in the current branch
# Usage: ./bin/lint-changed.sh [base-branch]

# Default base branch is 'main' or 'master'
BASE_BRANCH=${1:-$(git symbolic-ref refs/remotes/origin/HEAD | sed 's@^refs/remotes/origin/@@')}

# If we couldn't determine the default branch, use 'main'
if [ -z "$BASE_BRANCH" ]; then
  BASE_BRANCH="main"
fi

echo "Comparing current branch to $BASE_BRANCH..."

# Get list of changed JS files in the plugin directory
JS_FILES=$(git diff --name-only --diff-filter=ACMRT $BASE_BRANCH | grep -E "^plugin/.*\.(js|jsx|ts|tsx)$" || true)

# Get list of changed CSS files in the plugin directory
CSS_FILES=$(git diff --name-only --diff-filter=ACMRT $BASE_BRANCH | grep -E "^plugin/.*\.(css|scss|sass)$" || true)

# Check if there are any JS files to lint
if [ -z "$JS_FILES" ]; then
  echo "No JavaScript files changed."
else
  # Filter out files that don't exist
  EXISTING_JS_FILES=""
  for file in $JS_FILES; do
    if [ -f "$file" ]; then
      EXISTING_JS_FILES="$EXISTING_JS_FILES $file"
    else
      echo "Warning: File $file does not exist, skipping"
    fi
  done

  # Only run lint if there are existing files
  if [ -n "$EXISTING_JS_FILES" ]; then
    echo "Linting JavaScript files: $EXISTING_JS_FILES"
    cd plugin && npx wp-scripts lint-js $EXISTING_JS_FILES
  else
    echo "No existing JavaScript files to lint"
  fi
fi

# Check if there are any CSS files to lint
if [ -z "$CSS_FILES" ]; then
  echo "No CSS files changed."
else
  # Filter out files that don't exist
  EXISTING_CSS_FILES=""
  for file in $CSS_FILES; do
    if [ -f "$file" ]; then
      EXISTING_CSS_FILES="$EXISTING_CSS_FILES $file"
    else
      echo "Warning: File $file does not exist, skipping"
    fi
  done

  # Only run lint if there are existing files
  if [ -n "$EXISTING_CSS_FILES" ]; then
    echo "Linting CSS files: $EXISTING_CSS_FILES"
    cd plugin && npx wp-scripts lint-style $EXISTING_CSS_FILES
  else
    echo "No existing CSS files to lint"
  fi
fi
