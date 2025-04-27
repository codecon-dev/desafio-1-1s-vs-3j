PROJECT_NAME := $(shell grep '^name = ' Cargo.toml | cut -d'"' -f2)

TIMESTAMP := $(shell date +%Y%m%d%H%M)

BINARY_NAME := "run"

BIN_DIR := "entrypoint"

.PHONY: build clean

build:
	cargo build --release
	@mkdir -p $(BIN_DIR)
	@cp target/release/$(PROJECT_NAME) $(BIN_DIR)/$(BINARY_NAME)
	@echo "Binary copied to $(BIN_DIR)/$(BINARY_NAME)"

dev:
	@cargo watch -x run

run:
	@$(BIN_DIR)/$(BINARY_NAME)

clean:
	rm -rf target/
	rm -rf $(BIN_DIR)/*