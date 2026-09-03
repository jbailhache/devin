def premiers(n):
    trouves = []
    candidat = 2
    while len(trouves) < n:
        if all(candidat % p for p in trouves if p * p <= candidat):
            trouves.append(candidat)
        candidat += 1
    return trouves


if __name__ == "__main__":
    for i, p in enumerate(premiers(100), 1):
        print(f"{i:3d}: {p}")
