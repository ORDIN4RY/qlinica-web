with open("storage/logs/laravel.log", "r") as f:
    lines = f.readlines()
    print("".join(lines[-100:]))
