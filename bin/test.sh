#!/bin/bash

# Initialize variables
FILTER=""
VERBOSE=false
VERBOSE_FLAG_FILE="plugin/tests/verbose_logging_enabled"
RUN_JS_TESTS=false  # New variable for JS tests

# Parse command-line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        -v|--verbose)
            VERBOSE=true
            shift # Remove argument from processing
            ;;
        -j|--js)
            RUN_JS_TESTS=true
            shift # Remove argument from processing
            ;;
        -*)
            echo "Unknown option: $1"
            echo "Usage: $0 [-v|--verbose] [-j|--js] [test_filter]"
            exit 1
            ;;
        *)
            # Assume this is the test filter
            FILTER="$1"
            shift # Remove argument from processing
            ;;
    esac
done

# Create or remove the verbose logging flag file
if [ "$VERBOSE" = true ]; then
    touch "$VERBOSE_FLAG_FILE"
    echo "Verbose logging enabled"
else
    rm -f "$VERBOSE_FLAG_FILE"
fi

if [ "$RUN_JS_TESTS" = true ]; then
    # Run JavaScript tests
    echo "Running JavaScript tests"

    # Change to the plugin directory
    cd plugin

    # Add filter if specified
    if [ -n "$FILTER" ]; then
        echo "Running tests for: $FILTER"
        npx wp-scripts test --testPathPattern="$FILTER"
    else
        echo "Running all JavaScript tests"
        npx wp-scripts test
    fi
else
    # Run PHP tests
    # Build the command
    CMD="php -d memory_limit=256M vendor/bin/phpunit"

    # Add filter if specified
    if [ -n "$FILTER" ]; then
        CMD="$CMD --filter=$FILTER"
        echo "Running tests for: $FILTER"
    else
        echo "Running all PHP tests"
    fi

    # Execute the command
    wp-env run tests-cli --env-cwd=wp-content/plugins/wpcloud-station-plugin $CMD
fi

# Clean up
rm -f "$VERBOSE_FLAG_FILE"
