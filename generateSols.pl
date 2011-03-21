createMatrix(N,M,R) :- creaMatriu(N,M,A), append(A,R).
creaMatriu(N,0,[T]) :- creafila(N,0,T),!.
creaMatriu(N,M,[T|C]) :- creafila(N,M,T), M1 is M-1, creaMatriu(N,M1,C).
creafila(0,M,[p(M,0,_)]):-!.
creafila(N,M,[p(M,N,_)|C]) :-N1 is N-1,creafila(N1,M,C).


resolve :- forall(soluzione(S), writeln(S)).

disponi([], _, _, D, D) :- !.

disponi([N|T], As, Ps, D, S) :- member(p(X,Y,N), Ps),
								%no_vicini(p(X,Y,N),D),
                                collegati(N, As, C), 
                                segmenti(As, [p(X,Y,N)|D], Ss),
                                punto_libero(X, Y, Ss, D),
                                ok(p(X,Y,N), As, C, Ss, D),
                                disponi(T, As, Ps, [p(X,Y,N)|D], S).

punto_libero(X, Y, Ss, D) :- \+(member(p(X,Y,_), D)), \+(invasione_segmenti(X, Y, Ss)).

invasione_segmenti(X, Y, [S|_]) :- punto_sul_segmento(X, Y, S), !.
invasione_segmenti(X, Y, [_|T]) :- invasione_segmenti(X, Y, T).

% segmento parallelo ad asse Y
punto_sul_segmento(X, Y, S) :- S = seg(X, Ya, X, Yb), !, 
                               min(Ya, Yb, Ymin), max(Ya, Yb, Ymax), Y > Ymin, Y < Ymax.

% segmento parallelo ad asse X 	
punto_sul_segmento(X, Y, S) :- S = seg(Xa, Y, Xb, Y), !, 
                               min(Xa, Xb, Xmin), max(Xa, Xb, Xmax), X > Xmin, X < Xmax.

% segmento obliquo
punto_sul_segmento(X, Y, S) :- S = seg(Xa, Ya, Xb, Yb), !, 
                               min(Xa, Xb, Xmin), max(Xa, Xb, Xmax), 
                               min(Ya, Yb, Ymin), max(Ya, Yb, Ymax), 
                               parametri_retta(S, M, Q),
                               Y is (M * X) + Q,
                               X > Xmin, X < Xmax, Y > Ymin, Y < Ymax.

collegati(N, As, C) :- linked(N, As, [], C).

linked(_, [], L, L).
linked(N, [arco(N,N1)|T], L, C) :- linked(N, T, [N1|L], C), !.
linked(N, [arco(N1,N)|T], L, C) :- linked(N, T, [N1|L], C), !.
linked(N, [_|T], L, C) :- linked(N, T, L, C).

no_vicini(p(X,Y,_),D) :- X1 is X+1, Y1 is Y+1,
						 X2 is X-1, Y2 is Y-1,
						 \+member(p(X1,Y1,_),D),
						 \+member(p(X1,Y,_),D),
						 \+member(p(X,Y1,_),D),
						 \+member(p(X2,Y2,_),D),
						 \+member(p(X2,Y,_),D),
						 \+member(p(X,Y2,_),D),
						 \+member(p(X1,Y2,_),D),
						 \+member(p(X2,Y1,_),D).

ok(_, _, [], _, _) :- !.

ok(p(X,Y,N), As, [Nc|T], Ss, D) :- member(p(Xc,Yc,Nc), D),
                                   Sc = seg(X,Y,Xc,Yc), 
                                   \+(ingloba_punti(Sc, D)), 
                                   \+(intersecanti(Sc, Ss)), 
                                   ok(p(X,Y,N), As, T, Ss, D), !.

ok(p(X,Y,N), As, [Nc|T], Ss, D) :- \+(member(p(_,_,Nc), D)), ok(p(X,Y,N), As, T, Ss, D).

segmenti(As, D, Ss) :- accumula(As, D, [], Ss).

accumula([], _, Acc, Acc).
accumula([arco(N1,N2)|T], D, Acc, Ss) :- member(p(X1,Y1,N1), D), member(p(X2,Y2,N2), D), accumula(T, D, [seg(X1,Y1,X2,Y2)|Acc], Ss), !.
accumula([_|T], D, Acc, Ss) :- accumula(T, D, Acc, Ss), !.

ingloba_punti(Sc, D) :- member(p(Xd,Yd,_), D), punto_sul_segmento(Xd, Yd, Sc).

intersecanti(S1, [S2|_]) :- \+(stesso_segmento(S1,S2)), interseca(S1, S2), !.
intersecanti(S1, [_|T]) :- intersecanti(S1, T).

stesso_segmento(seg(Xa,Ya,Xb,Yb), seg(Xa,Ya,Xb,Yb)).
stesso_segmento(seg(Xa,Ya,Xb,Yb), seg(Xb,Yb,Xa,Ya)).

% segmenti ortogonali
interseca(S1, S2) :- S1 = seg(X1,_,X1,_), S2 = seg(_,Y2,_,Y2), !, punto_sui_segmenti(X1, Y2, S1, S2).

% segmenti ortogonali
interseca(S1, S2) :- S1 = seg(_,Y1,_,Y1), S2 = seg(X2,_,X2,_), !, punto_sui_segmenti(X2, Y1, S1, S2).

% segmenti sulla stessa retta parallela ad asse X
interseca(S1, S2) :- S1 = seg(Xa1,Y,Xb1,Y), S2 = seg(Xa2,Y,Xb2,Y), !,
                          max(Xa1, Xb1, Xmax1), max(Xa2, Xb2, Xmax2), min(Xa1, Xb1, Xmin1), min(Xa2, Xb2, Xmin2), Xmax1 > Xmin2, Xmax2 > Xmin1, !.

% segmenti sulla stessa retta parallela ad asse Y
interseca(S1, S2) :- S1 = seg(X,Ya1,X,Yb1), S2 = seg(X,Ya2,X,Yb2), !,
                     max(Ya1, Yb1, Ymax1), max(Ya2, Yb2, Ymax2), min(Ya1, Yb1, Ymin1), min(Ya2, Yb2, Ymin2),
                     Ymax1 > Ymin2, Ymax2 > Ymin1, !.

% un solo segmento parallelo ad asse Y
interseca(S1, S2) :- S2 = seg(X,_,X,_), !,
                     parametri_retta(S1, M1, Q1), Y is (M1 * X) + Q1, punto_sui_segmenti(X, Y, S1, S2), !.

% un solo segmento parallelo ad asse Y
interseca(S1, S2) :- S1 = seg(X,_,X,_), !, 
                     parametri_retta(S2, M2, Q2), Y is (M2 * X) + Q2, punto_sui_segmenti(X, Y, S1, S2), !.

interseca(S1, S2) :- punto_intersezione(S1, S2, X, Y), punto_sui_segmenti(X, Y, S1, S2).

punto_sui_segmenti(X, Y, S1, S2) :- S1 = seg(Xa1,Ya1,Xb1,Yb1), S2 = seg(Xa2,Ya2,Xb2,Yb2),
                                    min(Xa1, Xb1, Xmin1), max(Xa1, Xb1, Xmax1),
                                    min(Ya1, Yb1, Ymin1), max(Ya1, Yb1, Ymax1),
                                    min(Xa2, Xb2, Xmin2), max(Xa2, Xb2, Xmax2),
                                    min(Ya2, Yb2, Ymin2), max(Ya2, Yb2, Ymax2),
                                    contenuto(X, Xmin1, Xmax1), contenuto(Y, Ymin1, Ymax1),  
                                    contenuto(X, Xmin2, Xmax2), contenuto(Y, Ymin2, Ymax2). 
min(A, B, A) :- A =< B, !.
min(A, B, B) :- B < A.
max(A, B, A) :- A >= B, !.
max(A, B, B) :- B > A.

contenuto(V, V, V) :- !.
contenuto(V, Vmin, Vmax) :- Vmin =\= Vmax, !, V > Vmin, V < Vmax.

punto_intersezione(S1, S2, X, Y) :- parametri_retta(S1, M1, Q1),
                                    parametri_retta(S2, M2, Q2),
                                    M1 =\= M2,
                                    X is (Q2 - Q1) / (M1 - M2),
                                    Y is ((M1 * Q2) - (M2 * Q1)) / (M1 - M2).

parametri_retta(S, M, Q) :- S = seg(Xa,Ya,Xb,Yb), Xa =\= Xb, M is (Ya - Yb)/(Xa - Xb), Q is ((Xa * Yb) - (Xb * Ya)) / (Xa - Xb).