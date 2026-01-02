# Files and Folders to Delete

The following Android app-related files are no longer needed and should be deleted:

## Folders to Delete

```
App Source Code/
```

This entire folder can be removed as we're now using a web-based phishing approach instead of an APK.

## Files to Delete in Root

- `.github/workflows/gradle.yml` - Android build workflow
- `settings.gradle` - Gradle settings for Android
- `gradlew` - Gradle wrapper script
- `gradlew.bat` - Gradle wrapper batch file
- `build.gradle` - Gradle build configuration
- `App Source Code.iml` - IntelliJ module file

## Folders to Delete in Root

- `.gradle/` - Gradle cache
- `.idea/` - IntelliJ IDEA settings
- `gradle/` - Gradle wrapper files

## What to Keep

### Essential Files:

- `Web Server Files/` - All PHP files for phishing
- `LICENSE` - GPL license
- `README.md` - Updated documentation
- `.gitignore` - Git ignore rules
- `.env.example` - Environment template
- `railway.json` - Railway config
- `nixpacks.toml` - Nixpacks config
- `Procfile` - Process config

### Summary

**DELETE**: Everything related to Android app development
**KEEP**: Only web server files, configs, and documentation
