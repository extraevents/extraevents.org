import copy
import random
def parity(perm): #computes the parity of a permutation
    i=0
    swaps=0
    perm=perm[:]
    while i<len(perm)-1:
        if perm[i]==i:
            i+=1
        else:
            x,y=perm[i],perm[perm[i]]
            perm[x],perm[i]=x,y
            swaps+=1
    return swaps%2
#list of moves. each lists the edge that is flipped, the three pairs of corner stickers that are
#swapped (the latter two of which are centers that are swapped), the corners that are swapped,
# and whether or not the corners change orientation.

#edges are numbered as you see here
#corners are numbered clockwise on U face (looking from top) and clockwise on D face (looking on bottom)
#centers are numbered clockwise from top left when looking at each face, face order UFRBLD
truemoves={"UL":[0,(4,13),(0,17),(3,16),(3,0),True],"UB":[1,(9,16),(0,12),(1,13),(0,1),True],"UR":[2,(5,12),(1,8),(2,9),(1,2),True],
       "UF":[3,(8,17),(2,4),(3,5),(2,3),True],"LF":[4,(3,20),(4,18),(7,17),(3,4),False],"LB":[5,(0,23),(13,19),(14,16),(0,7),False],
       "RB":[6,(1,22),(9,15),(10,12),(1,6),False],"RF":[7,(2,21),(5,11),(6,8),(2,5),False],"DL":[8,(7,14),(19,20),(18,23),(7,4),True],
       "DB":[9,(10,19),(14,22),(15,23),(6,7),True],"DR":[10,(6,15),(10,21),(11,22),(5,6),True],"DF":[11,(11,18),(6,20),(7,21),(4,5),True]}
#the 36 possible pairs of pairs of edges that can be swapped if 5 move conjugates are included as moves
moves=[[(0,17),(3,16)],[(0,17),(4,13)],[(3,16),(4,13)],[(0,12),(1,13)],[(1,13),(9,16)],[(0,12),(9,16)],
       [(2,9),(1,8)],[(2,9),(5,12)],[(1,8),(5,12)],[(3,5),(2,4)],[(3,5),(8,17)],[(2,4),(8,17)],
       [(4,18),(7,17)],[(4,18),(3,20)],[(7,17),(3,20)],[(14,16),(13,19)],[(14,16),(0,23)],[(13,19),(0,23)],
       [(10,12),(9,15)],[(10,12),(1,22)],[(9,15),(1,22)],[(6,8),(5,11)],[(6,8),(2,21)],[(5,11),(2,21)],
       [(19,20),(18,23)],[(19,20),(7,14)],[(18,23),(7,14)],[(15,23),(14,22)],[(15,23),(10,19)],[(14,22),(10,19)],
       [(11,22),(10,21)],[(11,22),(6,15)],[(10,21),(6,15)],[(7,21),(6,20)],[(7,21),(11,18)],[(6,20),(11,18)]]
#reference list of edge names
edges=['UL','UB','UR','UF','LF','LB','RB','RF','DL','DB','DR','DF']
#used for reference only when constructing final scrambles
edgeblock=[[2,8,3,5,1,4],[3,9,0,6,2,5],[0,10,1,7,3,6],[1,11,2,4,0,7],[5,7,0,11,3,8],[4,6,1,8,0,9],
           [5,7,2,9,1,10],[4,6,3,10,2,11],[0,10,4,9,5,11],[1,11,5,10,6,8],[2,8,6,11,7,9],[3,9,7,8,4,10]]
#2 functions for applying moves to positions. The first only takes in the 12 normal moves,
#the second takes any of the 36 moves.
def realapply(move,pos):
    #move is a string (i.e. "UF"), pos is a state of the puzzle
    a=truemoves[move]
    pos=copy.deepcopy(pos)
    pos[0][a[0]]=1-pos[0][a[0]]
    x,y=a[4][0],a[4][1]
    pos[1][x],pos[1][y]=pos[1][y],pos[1][x]
    if a[5]:
        pos[2][x],pos[2][y]=(pos[2][y]+1)%3,(pos[2][x]-1)%3
    else:
        pos[2][x],pos[2][y]=pos[2][y],pos[2][x]
    for i in range(2):
        pos[3][a[i+2][0]],pos[3][a[i+2][1]]=pos[3][a[i+2][1]],pos[3][a[i+2][0]]
    return pos
def apply(move,pos):
    #move is an integer between 0 and 35, pos is a state of the puzzle
    pos=copy.deepcopy(pos)
    a=truemoves[edges[move//3]]
    b=moves[move]
    pos[0][a[0]]=1-pos[0][a[0]]
    x,y=a[4][0],a[4][1]
    pos[1][x],pos[1][y]=pos[1][y],pos[1][x]
    if a[5]:
        pos[2][x],pos[2][y]=(pos[2][y]+1)%3,(pos[2][x]-1)%3
    else:
        pos[2][x],pos[2][y]=pos[2][y],pos[2][x]
    for i in range(2):
        pos[3][b[i][0]],pos[3][b[i][1]]=pos[3][b[i][1]],pos[3][b[i][0]]
    return pos
#These 5 functions allow us to assign a number to each state for easy lookup in the many lookup tables
def encode(pos):
    return sum(pos[i]*24**i for i in range(6))
def encode4(pos):
    code=0
    for i in range(4):
        code+=pos[3][i]*5**i
    code*=256
    for i in range(4):
        code+=pos[1][i]*4**i
    code*=81
    for i in range(4):
        code+=pos[2][i]*3**i
    code*=16
    for i in range(4):
        code+=pos[0][i]*2**i
    return code
def encode3(pos):
    return pos[0]+2*pos[1]+48*pos[2]+1152*pos[3]+9216*pos[4]+27648*pos[5]+55296*pos[6]+1327104*pos[7]+31850496*pos[8]+254803968*pos[9]
def encode2(pos):
    return encode3(pos)
def encode1(pos):
    return pos[0]+24*pos[1]+576*pos[2]+13824*pos[3]+331776*pos[4]+663552*pos[5]+1327104*pos[6]+2654208*pos[7]

#create all the lookup tables from information in external files
a=open("curvycopter_helper/phase0_1.txt")
i=0
phase0_1={}
for line in a:
    if i==0:
        curr=int(line)
    else:
        phase0_1[curr]=line
    i=1-i
a.close()
b=open("curvycopter_helper/phase0_2.txt")
i=0
phase0_2={}
for line in b:
    if i==0:
        curr=int(line)
    else:
        phase0_2[curr]=line
    i=1-i
b.close()
c=open("curvycopter_helper/phase0_3.txt")
i=0
phase0_3={}
for line in c:
    if i==0:
        curr=int(line)
    else:
        phase0_3[curr]=line
    i=1-i
c.close()
d=open("curvycopter_helper/phase1.txt")
i=0
phase1={}
for line in d:
    if i==0:
        curr=int(line)
    else:
        phase1[curr]=line
    i=1-i
d.close()
e=open("curvycopter_helper/phase2.txt")
i=0
phase2={}
for line in e:
    if i==0:
        curr=int(line)
    else:
        phase2[curr]=line
    i=1-i
e.close()
f=open("curvycopter_helper/phase3.txt")
i=0
phase3={}
for line in f:
    if i==0:
        curr=int(line)
    else:
        phase3[curr]=line
    i=1-i
f.close()
g=open("curvycopter_helper/phase4.txt")
i=0
phase4={}
for line in g:
    if i==0:
        curr=int(line)
    else:
        phase4[curr]=line
    i=1-i
g.close()
h=open("curvycopter_helper/shape.txt")
poss=[0]*654117
i=0
for line in h:
    poss[i]=line
    i+=1
h.close()
f = open("curvycopter_training_out.txt", 'w')
#f = open("curvycopter_out.txt", 'w')
for _ in range(1000):
    #construct a random valid cube shaped state
    start=[[0]*12,[0,1,2,3,4,5,6,7],[0]*8,[0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5]]
    for i in range(12):
        start[0][i]=random.randrange(2)
    random.shuffle(start[1])
    while parity(start[1])!=sum(start[0])%2:
        random.shuffle(start[1])
    for i in range(7):
        start[2][i]=random.randrange(3)
    start[2][7]=(-sum(start[2]))%3
    random.shuffle(start[3])

    #put 1 of each color in the first center orbit
    orbit1=[]
    used=[0,0,0,0,0,0]
    orbits=[[0,7,10,12,17,21],[1,6,8,13,19,20],[2,4,9,15,18,23],[3,5,11,14,16,22]]
    for i in range(4):
        for guy in orbits[i]:
            if used[start[3][guy]]==0:
                orbit1.append(guy)
                used[start[3][guy]]=1
    orbit1.sort()
    made=[]
    newmade=list(map(int,phase0_1[encode(orbit1)].split()))
    for guy in newmade:
        start=apply(guy,start)
    made+=newmade

    #put 1 of each color in the second center orbit
    orbit2=[]
    used=[0,0,0,0,0,0]
    for i in range(1,4):
        for guy in orbits[i]:
            if used[start[3][guy]]==0:
                orbit2.append(guy)
                used[start[3][guy]]=1
    orbit2.sort()
    newmade=list(map(int,phase0_2[encode(orbit2)].split()))
    for guy in newmade:
        start=apply(guy,start)
    made+=newmade

    #put 1 of each color in the last 2 orbits (while fixing center parity)
    orbit3=[]
    used=[0,0,0,0,0,0]
    for i in range(2,4):
        for guy in orbits[i]:
            if used[start[3][guy]]==0:
                orbit3.append(guy)
                used[start[3][guy]]=1
    orbit3.sort()
    curr=(parity([start[3][0],start[3][7],start[3][10],start[3][12],start[3][17],start[3][21]])+parity([start[3][1],start[3][6],start[3][8],start[3][13],start[3][19],start[3][20]]))%2
    locs=[2,4,9,15,18,23,3,5,11,14,16,22]
    perm=[]
    for guy in locs:
        if guy in orbit3:
            perm.append(start[3][guy])
        else:
            perm.append(start[3][guy]+6)
    curr+=parity(perm)
    curr%=2
    done=False
    if curr==1:
        for guy in orbit3:
            if not done:
                if guy in orbits[3]:
                    color=start[3][guy]
                    for boi in orbits[3]:
                        if start[3][boi]==color and boi!=guy:
                            orbit3.remove(guy)
                            orbit3.append(boi)
                            done=True
                            break
    if curr==1 and not done:
        dude=orbit3[0]
        color=start[3][dude]
        for guy in orbit3:
            if start[3][guy]==color:
                orbit3.remove(dude)
                orbit3.append(guy)
                break
    orbit3.sort()
    newmade=list(map(int,phase0_3[encode(orbit3)].split()))
    for guy in newmade:
        start=apply(guy,start)
    made+=newmade

    #fix parity along each edge orbit
    edgeorbit=[[0,1,4,6,10,11],[1,2,5,7,8,11],[2,3,4,6,8,9],[0,3,5,7,9,10]]
    parities=[sum(start[0][dude] for dude in guy)%2 for guy in edgeorbit]
    centers=[parity([start[3][dude] for dude in orbit]) for orbit in orbits]
    change=[int(parities[i]!=centers[i]) for i in range(4)]
    newmade=[]
    if change==[1,1,0,0]:
        newmade+=[4,5]
    elif change==[1,0,1,0]:
        newmade+=[13,14]
    elif change==[1,0,0,1]:
        newmade+=[1,2]
    elif change==[0,1,1,0]:
        newmade+=[7,8]
    elif change==[0,1,0,1]:
        newmade+=[16,17]
    elif change==[0,0,1,1]:
        newmade+=[10,11]
    elif change==[1,1,1,1]:
        newmade+=[4,5,10,11]
    for guy in newmade:
        start=apply(guy,start)
    made+=newmade

    #solve the D centers and edges
    pos=[0,0,0,0,start[0][8],start[0][9],start[0][10],start[0][11]] 
    for i in range(24):
        if start[3][i]==5:
            if i in orbits[1]:
                pos[0]=i
            elif i in orbits[0]:
                pos[1]=i
            elif i in orbits[3]:
                pos[2]=i
            else:
                pos[3]=i
    newmade=phase1[encode1(pos)].split()
    for move in newmade:
        start=realapply(move,start)
    made+=newmade

    #solve LF and RB edges, and centers and corners below them
    pos=[start[0][4],0,0,0,0,start[0][6],0,0,0,0]
    for guy in orbits[2]:
        if start[3][guy]==4:
            pos[1]=guy
        elif start[3][guy]==3:
            pos[7]=guy
    for guy in orbits[0]:
        if start[3][guy]==1:
            pos[2]=guy
        elif start[3][guy]==2:
            pos[6]=guy
    pos[3]=start[1].index(4)
    pos[4]=start[2][pos[3]]
    pos[8]=start[1].index(6)
    pos[9]=start[2][pos[8]]
    newmade=phase2[encode2(pos)].split()
    for move in newmade:
        start=realapply(move,start)
    made+=newmade

    #solve rest of F2L
    pos=[start[0][7],0,0,0,0,start[0][5],0,0,0,0]
    for guy in orbits[1]:
        if start[3][guy]==1:
            pos[1]=guy
        elif start[3][guy]==4:
            pos[7]=guy
    for guy in orbits[3]:
        if start[3][guy]==2:
            pos[2]=guy
        elif start[3][guy]==3:
            pos[6]=guy
    pos[3]=start[1].index(5)
    pos[4]=start[2][pos[3]]
    pos[8]=start[1].index(7)
    pos[9]=start[2][pos[8]]
    newmade=phase3[encode3(pos)].split()
    for move in newmade:
        start=realapply(move,start)
    made+=newmade

    #solve rest of puzzle
    newmade=phase4[encode4(start)].split()
    for move in newmade:
        start=realapply(move,start)
    made+=newmade

    #fix formatting
    made=made[::-1]
    for i in range(len(made)):
        if type(made[i])==str:
            made[i]+="3"
        else:
            real=made[i]//3
            addon=made[i]%3
            if addon==0:
                made[i]=edges[real]+"3"
            elif addon==1:
                made[i]=edges[real]+"+"
            else:
                made[i]=edges[real]+"-"
    made.append("*")            

    #add jumbling moves
    bonus=random.choice(poss).split()
    for i in range(len(bonus)):
        if bonus[i][-1]=="1":
            bonus[i]=bonus[i][:-1]
        elif bonus[i][-1]=="4":
            bonus[i]=bonus[i][:-1]+"2'"
        elif bonus[i][-1]=="5":
            bonus[i]=bonus[i][:-1]+"'"
    made+=bonus

    #change to SALOW notation (comment this out if you want the other notation)
    length=len(made)
    alt=length
    for guy in made:
        if guy[-1] in ["+","-"]:
            alt+=4
    for i in range(len(made)):
        if made[i][-1]=="3":
            made[i]=made[i][:-1]
        elif made[i][-1]=="2":
            made[i]=made[i][:-1]+"++"
        elif len(made[i])==2:
            made[i]+="+"
        elif made[i][-1]=="'":
            if len(made[i])==3:
                made[i]=made[i][:-1]+"-"
            else:
                made[i]=made[i][:-2]+"--"
        elif made[i][-1]=="+":
            ind=truemoves[made[i][:-1]][0]
            other1,other2=edgeblock[ind][2],edgeblock[ind][3]
            made[i]="("+edges[other1]+"+ "+edges[other2]+"+ "+edges[ind]+" "+edges[other1]+"- "+edges[other2]+"-"+")"
        elif made[i][-1]=="-":
            ind=truemoves[made[i][:-1]][0]
            o1,o2=edgeblock[ind][4],edgeblock[ind][5]
            made[i]="("+edges[o1]+"- "+edges[o2]+"- "+edges[ind]+" "+edges[o1]+"+ "+edges[o2]+"+"+")"      
    print _+1," ".join(made)
    if(_>0):
        f.write("\n")
    f.write(" ".join(made))
f.close()    
