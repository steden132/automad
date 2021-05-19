#!/bin/bash

# Run this script to checkout develop and start a fresh feature, bugfix or refactor branch.

if [[ $(git status -s) ]]
then
	echo "Working directory is not clean!"
	git status -s
	exit 0
fi

git checkout develop

echo "Choose type of branch:"
echo
echo "  1) Feature (default)"
echo "  2) Bugfix"
echo "  3) Refactor"
echo
read -n 1 -p "Please select a number or press Enter for a Feature: " option
echo

case $option in 
	1) branchType="feat";;
	2) branchType="fix";;
	3) branchType="refactor";;
	*) branchType="feat";;
esac

read -p "Please enter a name: " branchName

branch=$branchType/$( echo $branchName | sed -e 's/\(.*\)/\L\1/' | sed 's/ /_/g' )

git branch $branch
git checkout $branch