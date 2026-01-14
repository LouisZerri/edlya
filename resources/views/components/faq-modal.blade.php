<div id="faq-modal" class="fixed inset-0 z-50 hidden">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" data-faq-close></div>
    
    {{-- Modal --}}
    <div class="absolute inset-4 md:inset-auto md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 md:w-full md:max-w-2xl bg-white rounded-xl shadow-2xl flex flex-col max-h-[90vh]">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-slate-800">Aide & FAQ</h2>
                    <p class="text-sm text-slate-500">Questions fréquentes sur les états des lieux</p>
                </div>
            </div>
            <button type="button" data-faq-close class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        {{-- Recherche --}}
        <div class="px-6 py-4 border-b border-slate-200">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="faq-search" placeholder="Rechercher une question..." 
                    class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none">
            </div>
        </div>
        
        {{-- Contenu --}}
        <div class="flex-1 overflow-y-auto px-6 py-4">
            <div id="faq-content" class="space-y-4">
                {{-- Catégorie : Général --}}
                <div class="faq-category" data-category="general">
                    <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-primary-500 rounded-full"></span>
                        Général
                    </h3>
                    <div class="space-y-2">
                        <div class="faq-item" data-keywords="état des lieux edl définition">
                            <button type="button" class="faq-question w-full text-left px-4 py-3 bg-slate-50 hover:bg-slate-100 rounded-lg text-sm font-medium text-slate-700 transition-colors cursor-pointer">
                                Qu'est-ce qu'un état des lieux ?
                            </button>
                            <div class="faq-answer hidden px-4 py-3 text-sm text-slate-600">
                                L'état des lieux est un document obligatoire qui décrit l'état du logement et de ses équipements à l'entrée et à la sortie du locataire. Il permet de comparer l'état initial et final pour déterminer les éventuelles réparations à la charge du locataire.
                            </div>
                        </div>
                        <div class="faq-item" data-keywords="obligatoire loi légal">
                            <button type="button" class="faq-question w-full text-left px-4 py-3 bg-slate-50 hover:bg-slate-100 rounded-lg text-sm font-medium text-slate-700 transition-colors cursor-pointer">
                                L'état des lieux est-il obligatoire ?
                            </button>
                            <div class="faq-answer hidden px-4 py-3 text-sm text-slate-600">
                                Oui, depuis la loi ALUR de 2014, l'état des lieux est obligatoire pour toute location (vide ou meublée). Il doit être établi de manière contradictoire par le bailleur et le locataire, ou par un tiers mandaté.
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Catégorie : États --}}
                <div class="faq-category" data-category="etats">
                    <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                        États et dégradations
                    </h3>
                    <div class="space-y-2">
                        <div class="faq-item" data-keywords="neuf bon usagé mauvais état signification">
                            <button type="button" class="faq-question w-full text-left px-4 py-3 bg-slate-50 hover:bg-slate-100 rounded-lg text-sm font-medium text-slate-700 transition-colors cursor-pointer">
                                Que signifient les différents états ?
                            </button>
                            <div class="faq-answer hidden px-4 py-3 text-sm text-slate-600">
                                <ul class="space-y-2">
                                    <li><strong>Neuf :</strong> Élément jamais utilisé, en parfait état</li>
                                    <li><strong>Très bon :</strong> Élément en excellent état, quasi neuf</li>
                                    <li><strong>Bon :</strong> Élément en bon état général, usure normale légère</li>
                                    <li><strong>Usagé :</strong> Usure normale correspondant à l'ancienneté</li>
                                    <li><strong>Mauvais :</strong> Dégradations visibles nécessitant réparation</li>
                                    <li><strong>Hors service :</strong> Élément non fonctionnel</li>
                                </ul>
                            </div>
                        </div>
                        <div class="faq-item" data-keywords="vétusté usure normale dégradation différence">
                            <button type="button" class="faq-question w-full text-left px-4 py-3 bg-slate-50 hover:bg-slate-100 rounded-lg text-sm font-medium text-slate-700 transition-colors cursor-pointer">
                                Quelle différence entre usure normale et dégradation ?
                            </button>
                            <div class="faq-answer hidden px-4 py-3 text-sm text-slate-600">
                                <p class="mb-2"><strong>Usure normale (vétusté) :</strong> Détérioration naturelle due au temps et à l'usage normal. Elle est à la charge du bailleur.</p>
                                <p><strong>Dégradation :</strong> Détérioration anormale due à un mauvais usage, négligence ou accident. Elle est à la charge du locataire.</p>
                                <p class="mt-2 text-slate-500">Exemples : Une peinture qui s'écaille après 10 ans = usure normale. Un trou dans le mur = dégradation.</p>
                            </div>
                        </div>
                        <div class="faq-item" data-keywords="grille vétusté durée vie">
                            <button type="button" class="faq-question w-full text-left px-4 py-3 bg-slate-50 hover:bg-slate-100 rounded-lg text-sm font-medium text-slate-700 transition-colors cursor-pointer">
                                Comment fonctionne la grille de vétusté ?
                            </button>
                            <div class="faq-answer hidden px-4 py-3 text-sm text-slate-600">
                                La grille de vétusté définit la durée de vie théorique des équipements et leur taux d'abattement annuel. Par exemple :
                                <ul class="mt-2 space-y-1">
                                    <li>• Peinture : 7-10 ans</li>
                                    <li>• Moquette : 7 ans</li>
                                    <li>• Parquet : 25 ans</li>
                                    <li>• Robinetterie : 10 ans</li>
                                </ul>
                                <p class="mt-2">Si un élément est dégradé mais a dépassé sa durée de vie, le locataire ne paie qu'une partie des réparations.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Catégorie : Compteurs --}}
                <div class="faq-category" data-category="compteurs">
                    <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                        Compteurs
                    </h3>
                    <div class="space-y-2">
                        <div class="faq-item" data-keywords="compteur relever lire index">
                            <button type="button" class="faq-question w-full text-left px-4 py-3 bg-slate-50 hover:bg-slate-100 rounded-lg text-sm font-medium text-slate-700 transition-colors cursor-pointer">
                                Comment relever un compteur ?
                            </button>
                            <div class="faq-answer hidden px-4 py-3 text-sm text-slate-600">
                                <p>Relevez tous les chiffres affichés sur le compteur (index). Pour les compteurs électriques, notez séparément heures pleines (HP) et heures creuses (HC) si applicable.</p>
                                <p class="mt-2"><strong>Conseil :</strong> Prenez une photo du compteur montrant clairement l'index. Cela constitue une preuve en cas de litige.</p>
                            </div>
                        </div>
                        <div class="faq-item" data-keywords="compteur photo preuve">
                            <button type="button" class="faq-question w-full text-left px-4 py-3 bg-slate-50 hover:bg-slate-100 rounded-lg text-sm font-medium text-slate-700 transition-colors cursor-pointer">
                                Pourquoi photographier les compteurs ?
                            </button>
                            <div class="faq-answer hidden px-4 py-3 text-sm text-slate-600">
                                La photo du compteur sert de preuve en cas de contestation sur la consommation entre l'entrée et la sortie. Elle permet de vérifier l'index noté et d'éviter les erreurs de saisie.
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Catégorie : Clés --}}
                <div class="faq-category" data-category="cles">
                    <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                        Clés
                    </h3>
                    <div class="space-y-2">
                        <div class="faq-item" data-keywords="clé perdue manquante">
                            <button type="button" class="faq-question w-full text-left px-4 py-3 bg-slate-50 hover:bg-slate-100 rounded-lg text-sm font-medium text-slate-700 transition-colors cursor-pointer">
                                Que faire si des clés sont perdues ?
                            </button>
                            <div class="faq-answer hidden px-4 py-3 text-sm text-slate-600">
                                Si le locataire ne restitue pas toutes les clés, le bailleur peut lui facturer le remplacement des clés et éventuellement du cylindre de serrure pour des raisons de sécurité. Le coût varie de 50€ à 300€ selon le type de serrure.
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Catégorie : Signature --}}
                <div class="faq-category" data-category="signature">
                    <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                        Signature
                    </h3>
                    <div class="space-y-2">
                        <div class="faq-item" data-keywords="signature électronique valide légal">
                            <button type="button" class="faq-question w-full text-left px-4 py-3 bg-slate-50 hover:bg-slate-100 rounded-lg text-sm font-medium text-slate-700 transition-colors cursor-pointer">
                                La signature électronique est-elle valide ?
                            </button>
                            <div class="faq-answer hidden px-4 py-3 text-sm text-slate-600">
                                Oui, la signature électronique a la même valeur juridique que la signature manuscrite (articles 1366 et 1367 du Code civil). Edlya assure la traçabilité complète : horodatage, vérification email, adresse IP.
                            </div>
                        </div>
                        <div class="faq-item" data-keywords="refus signer locataire">
                            <button type="button" class="faq-question w-full text-left px-4 py-3 bg-slate-50 hover:bg-slate-100 rounded-lg text-sm font-medium text-slate-700 transition-colors cursor-pointer">
                                Que faire si le locataire refuse de signer ?
                            </button>
                            <div class="faq-answer hidden px-4 py-3 text-sm text-slate-600">
                                <p>Si le locataire refuse de signer, vous pouvez faire appel à un huissier de justice pour établir un état des lieux contradictoire. Le coût est partagé entre les parties.</p>
                                <p class="mt-2">Sans état des lieux d'entrée, le logement est présumé avoir été remis en bon état au locataire.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Catégorie : Photos --}}
                <div class="faq-category" data-category="photos">
                    <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2">
                        <span class="w-2 h-2 bg-pink-500 rounded-full"></span>
                        Photos
                    </h3>
                    <div class="space-y-2">
                        <div class="faq-item" data-keywords="photo obligatoire combien">
                            <button type="button" class="faq-question w-full text-left px-4 py-3 bg-slate-50 hover:bg-slate-100 rounded-lg text-sm font-medium text-slate-700 transition-colors cursor-pointer">
                                Combien de photos faut-il prendre ?
                            </button>
                            <div class="faq-answer hidden px-4 py-3 text-sm text-slate-600">
                                <p>Il n'y a pas de nombre obligatoire, mais il est recommandé de photographier :</p>
                                <ul class="mt-2 space-y-1">
                                    <li>• Une vue d'ensemble de chaque pièce</li>
                                    <li>• Chaque dégradation ou défaut constaté</li>
                                    <li>• Les compteurs</li>
                                    <li>• Les équipements (cuisine, salle de bain)</li>
                                    <li>• Les clés remises</li>
                                </ul>
                                <p class="mt-2">En moyenne : 20-40 photos pour un appartement.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Aucun résultat --}}
            <div id="faq-no-results" class="hidden text-center py-8">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-slate-500">Aucune question trouvée</p>
                <p class="text-sm text-slate-400 mt-1">Essayez avec d'autres mots-clés</p>
            </div>
        </div>
        
        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 rounded-b-xl">
            <p class="text-xs text-slate-500 text-center">
                Une question non traitée ? Contactez-nous à <a href="mailto:contact@gestimmo-presta.fr" class="text-primary-600 hover:underline">contact@gestimmo-presta.fr</a>
            </p>
        </div>
    </div>
</div>