from itertools import permutations, product
# from collections import Counter
import random
import functools
import datetime

def factorial(n):
    """Return the factorial of a nonnegative integer."""
    if n < 2:
        return 1
    f = 2
    for i in range(3,n+1):
        f *= i
    return f

def C(n, k):
    if k < 0 or k > n:
        return 0
    if k == 0 or k == n:
        return 1
    c = 1
    for i in range(k):
        c = c * (n - i) // (i + 1)
    return c

def permutation_to_index(perm):
    n = len(perm)
    f = factorial(n-1)
    ind = 0
    while n > 1:
        ind += perm[0]*f
        perm = list(map(lambda x:x-(x>perm[0]),perm[1:]))
        n -= 1
        f //= n
    return ind

def index_to_permutation(ind,n):
    perm = []
    p = list(range(n))
    f = factorial(n-1)
    for i in range(n-1):
        perm += [p.pop(ind//f)]
        ind %= f
        f //= n-1-i
    return tuple(perm+p)

def permutation_parity(A):
    n = len(A)
    parity = 0
    for i in range(n):
        for j in range(i, n):
            if A[i] > A[j]:
                parity ^= 1
    return parity

def index_to_evenpermutation(ind, n):
    perm = index_to_permutation(ind * 2, n)
    if permutation_parity(perm) == 1:
        perm = perm[:-2] + perm[:-3:-1]
    return perm

def compose(a, b):
    return tuple(a[x] for x in b)

def comb_to_index(l):
    """Represent a list of bits (big-endian) as an index among all lists with the same sum."""
    bits = len(l)
    ones = sum(l)
    zeros = bits-ones
    if zeros == 0 or ones == 0 or bits == 1:
        return 0
    b = C(bits-1,ones)
    ind = 0
    while zeros > 0 and ones > 0 and bits > 1:
        bits -= 1
        if l[0] == 0:
            zeros -= 1
            b = b*zeros//bits
        else:
            ind += b
            b = b*ones//bits
            ones -=1
        l = l[1:]
    return ind

def index_to_comb(ind, ones, bits=None):
    """Look up an index among all bit lists with the same sum."""
    if ind == 0:
        if bits is None:
            return (1,)*ones
        return (0,)*(bits-ones)+(1,)*ones
    if bits is None:
        bits = ones
        b = C(bits,ones)
        while ind >= b:
            bits += 1
            b = C(bits,ones)
    zeros = bits-ones
    b = C(bits-1,ones)
    l = []
    for i in range(bits-1):
        bits -= 1
        if ind < b:
            l.append(0)
            zeros -= 1
            b = b*zeros//bits
        else:
            l.append(1)
            ind -= b
            b = b*ones//bits
            ones -= 1
    return tuple(l+[ones])

class TwistedPermutation(object):
    def __init__(self, permutation, orientation, modulus):
        self.p = tuple(permutation)
        self.o = tuple(orientation)
        self.m = modulus

    def __eq__(self, other):
        if type(other) is not type(self):
            return False
        return self.p == other.p and self.o == other.o and self.m == other.m

    def __hash__(self):
        return hash((self.p, self.o, self.m))

    def __len__(self):
        return len(self.p)

    def __mul__(self, other):
        if not isinstance(other, TwistedPermutation):
            return NotImplemented
        p = tuple(self.p[other.p[i]] for i in range(len(self)))
        o = tuple((self.o[other.p[i]] + other.o[i]) % self.m for i in range(len(self)))
        return type(self)(p, o, self.m)

    def invert(self):
        p = [None] * len(self)
        o = [None] * len(self)
        for i in range(len(self)):
            p[self.p[i]] = i
            o[self.p[i]] = -self.o[i] % self.m
        return type(self)(p, o, self.m)

    def __pow__(self, n):
        if type(n) is not int:
            return NotImplemented
        if n == 0:
            return type(self)(range(len(self)), (0, ) * len(self), self.m)
        elif n == 1:
            return self
        elif n < 0:
            return self.invert() ** -n
        else:
            half = self ** (n // 2)
            return half * half * self ** (n % 2)

    def __repr__(self):
        return 'TwistedPermutation(%r, %r, %r)' % (self.p, self.o, self.m)

    def __str__(self):
        return repr(self)

class State(TwistedPermutation):
    def __init__(self, *args, **kwargs):
        if 'index' in kwargs or (len(args) == 1 and len(kwargs) == 0 and isinstance(args[0], int)):
            index = kwargs['index'] if 'index' in kwargs else args[0]
            index_p, index_o = divmod(index, 3 ** 19)
            self.p = index_to_evenpermutation(index_p, 20)
            o = [index_o // 3**i % 3 for i in range(19)]
            o.append(-sum(o) % 3)
            self.o = tuple(o)
            self.m = 3
            return
        if 'modulus' not in kwargs and len(args) < 3:
            kwargs['modulus'] = 3
        super().__init__(*args, **kwargs)

    def __index__(self):
        index_p = permutation_to_index(self.p) // 2
        index_o = sum(self.o[i] * 3**i for i in range(19))
        return index_o + 3**19 * index_p

    def __repr__(self):
        return 'State(%r, %r)' % (self.p, self.o)

def permutation_from_cycles(cycles, n):
    if len(cycles) > 0 and type(cycles[0]) == int:
        cycles = (cycles,)
    p = list(range(n))
    for cycle in cycles:
        for i in range(len(cycle)):
            p[cycle[i]] = cycle[(i + 1) % len(cycle)]
    return tuple(p)

def unsparsify_list(d, n):
    l = [0] * n
    for (k, v) in d.items():
        l[k] = v
    return l

move_U = State(permutation_from_cycles((0, 1, 2, 3, 4), 20), (0,) * 20)
move_R = State(permutation_from_cycles((4, 3, 11, 12, 13), 20), unsparsify_list({4: 2, 3: 1, 11: 1, 12: 1, 13: 1}, 20))
move_F = State(permutation_from_cycles((3, 2, 9, 10, 11), 20), unsparsify_list({3: 2, 2: 1, 9: 1, 10: 1, 11: 1}, 20))
move_L = State(permutation_from_cycles((2, 1, 7, 8, 9), 20), unsparsify_list({2: 2, 1: 1, 7: 1, 8: 1, 9: 1}, 20))
move_BL = State(permutation_from_cycles((1, 0, 5, 6, 7), 20), unsparsify_list({1: 2, 0: 1, 5: 1, 6: 1, 7: 1}, 20))
move_BR = State(permutation_from_cycles((0, 4, 13, 14, 5), 20), unsparsify_list({0: 2, 4: 1, 13: 1, 14: 1, 5: 1}, 20))
move_x2 = State((15, 16, 17, 18, 19, 10, 9, 8, 7, 6, 5, 14, 13, 12, 11, 0, 1, 2, 3, 4), (0,) * 20)

moves = (move_U, move_R, move_F, move_L, move_BL, move_BR, move_x2)
move_names = ('U', 'R', 'F', 'L', 'BL', 'BR', 'flip')

test_state = State(888942217053048776751679251)

def random_state():
    return State(random.randrange(factorial(20) // 2 * 3 ** 19))

def print_move_sequence(move_sequence):
    s = []
    for (m, r) in move_sequence:
        suffix = (None, '', '2', "2'", "'")[r]
        s.append(move_names[m] + suffix)
    return ' '.join(s)

def apply_move_sequence(state, move_sequence):
    for (m, r) in move_sequence:
        state = state * moves[m]**r
    return state

'''
Now for the actual solver. We fix some standard colour scheme to solve into
(white on top, green on front, etc.), rather than solving into an arbitrary
orientation and then rotating. I think my mega has a somewhat uncommon colour
scheme so to prevent confusion I'll just call the bottom colour anti-white.

Phase 1: get the five corners with the anti-white colour out of the U layer.
(6-gen)

(rotate!)

Phase 2: solve the five corners with the anti-white colour into the U layer.
(6-gen)

(rotate again!)

Phase 3: solve five more corners at the back/left to reduce to <U,R,F>.
(6-gen)

Phase 4: finish.
(3-gen)

Phase 1 can be done with blind brute force. If all five anti-white pieces are
already not on the D layer (~19% chance), then we can just skip this and the
rotation.

Phases 2 and 3 can make use of exactly the same move tables since they're both
moving five pieces around 6-gen. This has 15!/10! 3^5 ~ 88 mil states, but we
can't generate the whole move table in Python in a reasonable amount of time,
so this is split into orientation + permutation.
'''

def solve_phase1(state):
    p = state.p
    if all(p.index(i) < 15 for i in range(15, 20)):
        return ()
    if all(p.index(i) >= 5 for i in range(15, 20)):
        return ((6, 1),)
    flags = tuple(+(p[i] >= 15) for i in range(20))
    depth = 0
    sol = None
    while sol is None:
        depth += 1
        sol = search_phase1(flags, depth)
    return sol + ((6, 1),)

def search_phase1(flags, depth, last=None):
    if depth == 0:
        if any(flags[i] for i in range(5)):
            return None
        return ()
    for move_index in range(6):
        if move_index == last:
            continue
        for r in range(1, 5):
            new_flags = compose(flags, (moves[move_index] ** r).p)
            sol = search_phase1(new_flags, depth-1, move_index)
            if sol is not None:
                return ((move_index, r),) + sol
    return None

def index_phase2(state):
    p = state.p
    o = state.o
    index_c = comb_to_index([+(p[i] >= 15) for i in range(15)])
    index_o = sum(o * 3**i for i, o in enumerate(o[i] for i in range(15) if p[i] >= 15)) + 243 * index_c
    index_p = sum(p.index(15 + i) * 15**i for i in range(5))
    return index_o, index_p

def solve_phase2(state):
    mtables = (generate_phase23_orientation_mtable(), generate_phase23_permutation_mtable())
    ptables = (generate_phase2_orientation_ptable(), generate_phase2_permutation_ptable())
    return ida_solve(index_phase2(state), mtables, ptables) + ((6, 1),)

def index_phase3(state):
    p = state.p
    o = state.o
    index_c = comb_to_index([+(p[i] in (5,6,7,8,14)) for i in range(15)])
    index_o = sum(o * 3**i for i, o in enumerate(o[i] for i in range(15) if p[i] in (5,6,7,8,14))) + 243 * index_c
    index_p = sum(p.index((5,6,7,8,14)[i]) * 15**i for i in range(5))
    return index_o, index_p

def solve_phase3(state):
    mtables = (generate_phase23_orientation_mtable(), generate_phase23_permutation_mtable())
    ptables = (generate_phase3_orientation_ptable(), generate_phase3_permutation_ptable())
    return ida_solve(index_phase3(state), mtables, ptables)

def index_phase4(state):
    p = state.p
    o = state.o
    index_o = sum(x * 3**i for i, x in enumerate(o[:5] + o[9:13]))
    index_p = permutation_to_index([x if x < 5 else x - 4 for x in p[:5] + p[9:14]]) // 2
    return index_o, index_p

def solve_phase4(state):
    mtables = (generate_phase4_orientation_mtable(), generate_phase4_permutation_mtable())
    ptables = (generate_phase4_orientation_ptable(), generate_phase4_permutation_ptable())
    return ida_solve(index_phase4(state), mtables, ptables)

def solve(state):
    sol1 = solve_phase1(state)
    state = apply_move_sequence(state, sol1)
    sol2 = solve_phase2(state)
    state = apply_move_sequence(state, sol2)
    sol3 = solve_phase3(state)
    state = apply_move_sequence(state, sol3)
    sol4 = solve_phase4(state)
    return sol1 + sol2 + sol3 + sol4

@functools.lru_cache()
def generate_phase23_orientation_mtable():
    phase23_move_o = [[None] * 6 for i in range(C(15, 5) * 3**5)]
    combs = tuple(index_to_comb(i, 5, 15) for i in range(C(15, 5)))
    for i in range(C(15, 5)):
        comb = combs[i] + (0,) * 5
        new_combs = tuple(comb_to_index(compose(comb, moves[move_index].p)[:15]) for move_index in range(6))
        for j in range(3**5):
            orient = [j // 3**i % 3 for i in range(4, -1, -1)]
            orient_full = [orient.pop() if comb[i] == 1 else 99 for i in range(20)]
            for move_index in range(6):
                new_orient_full = (orient_full[moves[move_index].p[i]] + moves[move_index].o[i] for i in range(15))
                new_orient = filter(lambda x: x < 10, new_orient_full)
                J = sum(o % 3 * 3**i for i, o in enumerate(new_orient))
                phase23_move_o[j + 3**5 * i][move_index] = J + 3**5 * new_combs[move_index]
    return phase23_move_o

@functools.lru_cache()
def generate_phase2_orientation_ptable():
    mtable = generate_phase23_orientation_mtable()
    return bfs(mtable, (243 * 3002,))

@functools.lru_cache()
def generate_phase3_orientation_ptable():
    mtable = generate_phase23_orientation_mtable()
    return bfs(mtable, (243 * 246,))

@functools.lru_cache()
def generate_phase23_permutation_mtable():
    phase23_move_p = [[None] * 6 for i in range(15 ** 5)]
    single = [[None] * 6 for i in range(15)]
    for i in range(15):
        for move_index in range(6):
            single[i][move_index] = moves[move_index].p.index(i)
    for locations in product(range(15), repeat=5):
        ind = sum(locations[i] * 15 ** i for i in range(5))
        for move_index in range(6):
            new_locations = [single[loc][move_index] for loc in locations]
            new_ind = sum(new_locations[i] * 15 ** i for i in range(5))
            phase23_move_p[ind][move_index] = new_ind
    return phase23_move_p

@functools.lru_cache()
def generate_phase2_permutation_ptable():
    mtable = generate_phase23_permutation_mtable()
    return bfs(mtable, (sum(i * 15**i for i in range(5)),))

@functools.lru_cache()
def generate_phase3_permutation_ptable():
    mtable = generate_phase23_permutation_mtable()
    return bfs(mtable, (sum((5,6,7,8,14)[i] * 15**i for i in range(5)),))

@functools.lru_cache()
def generate_phase4_orientation_mtable():
    mtable = [[None] * 3 for i in range(3**9)]
    for i in range(3**9):
        o = [i // 3**j % 3 for j in range(9)]
        o.append(-sum(o) % 3)
        o = o[:5] + [0, 0, 0, 0] + o[5:]
        for move_index in range(3):
            move = moves[move_index]
            new_o = [(o[move.p[i]] + move.o[i]) % 3 for i in [0,1,2,3,4,9,10,11,12,13]]
            new_i = sum(new_o[i] * 3**i for i in range(9))
            mtable[i][move_index] = new_i
    return mtable

@functools.lru_cache()
def generate_phase4_permutation_mtable():
    mtable = [[None] * 3 for i in range(factorial(10)//2)]
    for i in range(factorial(10)//2):
        p = index_to_evenpermutation(i, 10)
        p = [x if x < 5 else x + 4 for x in p]
        p = p[:5] + [5,6,7,8] + p[5:]
        for move_index in range(3):
            move = moves[move_index]
            new_p = compose(p, move.p[:14])
            new_p = [x if x < 5 else x - 4 for x in new_p[:5] + new_p[-5:]]
            mtable[i][move_index] = permutation_to_index(new_p) // 2
    return mtable

@functools.lru_cache()
def generate_phase4_orientation_ptable():
    mtable = generate_phase4_orientation_mtable()
    return bfs(mtable, (0,))

@functools.lru_cache()
def generate_phase4_permutation_ptable():
    mtable = generate_phase4_permutation_mtable()
    return bfs(mtable, (0,))

def bfs(mtable, goal_states):
    N = len(mtable)
    nmoves = len(mtable[0])
    ptable = [None] * N
    queue = list(goal_states)
    depth = 0
    while len(queue) > 0:
        new_queue = []
        for state in queue:
            if ptable[state] is not None:
                continue
            ptable[state] = depth
            for move_index in range(nmoves):
                new_state = mtable[state][move_index]
                while new_state != state:
                    new_queue.append(new_state)
                    new_state = mtable[new_state][move_index]
        queue = new_queue
        depth += 1
    return ptable

def ida_solve(indices, mtables, ptables):
    if type(indices) != list:
        indices = list(indices)
    ncoords = len(indices)
    bound = max(ptables[i][indices[i]] for i in range(ncoords))
    while True:
        path = ida_search(indices, mtables, ptables, bound, None)
        if path is not None:
            return path
        bound += 1

def ida_search(indices, mtables, ptables, bound, last):
    ncoords = len(indices)
    nmoves = len(mtables[0][0])
    heuristic = max(ptables[i][indices[i]] for i in range(ncoords))
    if heuristic > bound:
        return None
    if bound == 0 or heuristic == 0:
        return ()
    for m in range(nmoves):
        if m == last:
            continue
        new_indices = indices[:]
        for c in range(ncoords):
            new_indices[c] = mtables[c][indices[c]][m]
        r = 1
        while new_indices != indices:
            subpath = ida_search(new_indices, mtables, ptables, bound-1, m)
            if subpath is not None:
                return ((m, r),) + subpath
            for c in range(ncoords):
                new_indices[c] = mtables[c][new_indices[c]][m]
            r += 1
    return None




f = open("kilo_training_out.txt", 'w')
for i in range(1000):
    now = datetime.datetime.now()
    print(i)
    print(now.strftime("%d-%m-%Y %H:%M\n"))
    if i>0:
        f.write("\n")
    f.write(print_move_sequence(solve(random_state())))
f.close()