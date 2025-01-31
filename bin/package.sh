#!/usr/bin/env bash

set -Eeuo pipefail
script_dir=$(cd "$(dirname "${BASH_SOURCE[0]}")" &>/dev/null && pwd -P)
trap cleanup SIGINT SIGTERM ERR EXIT

cleanup() {
	rm -rf "$script_dir/temp"
}

package=$1
dist=${2:-""}
dist=$(echo "$(pwd)/$dist" | sed "s,/$,,")

if [ -z "$package" ]; then
	echo "Usage: $0 <plugin|theme|all>"
	exit 1
fi

function build() {
	local build_package=${1:-""}

	if [ "$build_package" != "plugin" ] && [ "$build_package" != "theme" &&  [ "$build_package" != "theme-pico" ] ] ; then
		echo "Invalid package type: $build_package"
		exit 1
	fi
	if [ ! -d "$dist" ]; then
		mkdir -p "$dist"
	fi

	if [ -f "$dist/$build_package.zip" ]; then
		rm "$dist/$build_package.zip"
	fi

	mkdir -p "$script_dir/temp/wpcloud-station"
	pushd "$build_package"
	zip -r "$script_dir/temp/wpcloud-station/$build_package.zip" . -x@.distignore
	popd
	pushd "$script_dir/temp/wpcloud-station"
	unzip "$build_package.zip"
	rm "$build_package.zip"
	cd ..
	zip -r "$dist/$build_package.zip" wpcloud-station
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