#!/usr/bin/env bash

set -Eeuo pipefail
script_dir=$(cd "$(dirname "${BASH_SOURCE[0]}")" &>/dev/null && pwd -P)
trap cleanup SIGINT SIGTERM ERR EXIT

cleanup() {
	if [ -d "$script_dir/temp" ] &&  [ -z $no_cleanup ]; then
		echo "Cleaning up $script_dir/temp"
		rm -rf "$script_dir/temp"
	fi
}

package=$1
dist=${2:-""}
no_cleanup=${3:-""}
dist=$(echo "$(pwd)/$dist" | sed "s,/$,,")

if [ -z "$package" ]; then
	echo "Usage: $0 <plugin|theme|all>"
	exit 1
fi

function build() {
	local build_package=${1:-""}

	if [ "$build_package" != "plugin" ] && [ "$build_package" != "theme" ] && [ "$build_package" != "theme-pico" ] ; then
		echo "Invalid package type: $build_package"
		exit 1
	fi
	if [ ! -d "$dist" ]; then
		mkdir -p "$dist"
	fi

	package_zip="wpcloud-station"
	if [ "$build_package" != "plugin" ] && [ "$build_package" != "theme" ]; then
		package_zip="$package_zip-$build_package"
	fi

	if [ -f "$dist/$package_zip.zip" ]; then
		rm "$dist/$package_zip.zip"
	fi

	temp="$script_dir/temp/$package_zip"

	mkdir -p "$temp"
	pushd "$build_package"
	zip -r "$temp/$package_zip.zip" . -x@.distignore
	popd
	pushd "$temp"
	unzip "$package_zip.zip"
	rm "$package_zip.zip"
	cd ..
	zip -r "$dist/$build_package.zip" "$package_zip"
	popd
}

if [ "$package" == "all" ]; then
	build "plugin"
	build "theme"
	build "theme-pico"
else
	build "$package"
fi

cleanup