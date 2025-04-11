import subprocess
import random
from datetime import datetime, timedelta

start_date = datetime(2023, 1, 1)
end_date = datetime(2024, 1, 1)

import os

# Remove .git/index.lock if it exists
lock_file = os.path.join(".git", "index.lock")
if os.path.exists(lock_file):
    os.remove(lock_file)

try:
    subprocess.run(["git", "add", "."], check=True)
except subprocess.CalledProcessError as e:
    print(f"❌ Failed to add files: {e}")
    exit(1)

result = subprocess.run(["git", "diff", "--cached", "--name-only"], capture_output=True, text=True)
files = result.stdout.strip().split("\n")

files = [file for file in files if file.strip()]

if not files:
    print("❌ No staged files found. Use `git add .` first.")
    exit()

for file in files:
    random_days = random.randint(0, (end_date - start_date).days)
    random_time = timedelta(
        hours=random.randint(0, 23),
        minutes=random.randint(0, 59),
        seconds=random.randint(0, 59)
    )
    commit_date = start_date + timedelta(days=random_days) + random_time

    commit_date_str = commit_date.strftime("%Y-%m-%d %H:%M:%S")

    subprocess.run([
        "git", "commit", "--date", commit_date_str, "-m", f"Updated {file}", "--", file
    ], check=True)

    print(f"✅ Committed {file} with date {commit_date_str}")

print("🎉 Done! All commits have been made.")
