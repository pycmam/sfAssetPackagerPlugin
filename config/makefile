
.PHONY += build-css clean-css

generated_makefile := plugins/sfAssetPackagerPlugin/config/makefile.mk
include $(generated_makefile)

# Обновлять makefile при любом изменении конфига
$(generated_makefile): \
  config/*.yml \
  apps/*/config/*.yml \
  plugins/sfAssetPackagerPlugin/config/*.yml \
  plugins/sfAssetPackagerPlugin/lib/task/sfAssetsBuildMakefileTask.class.php
	php symfony cc --type=config
	php symfony assets:build-makefile


# Build
build-css: $(asset_css_targets)

# Clean
clean-css:
	rm $(asset_css_targets)
