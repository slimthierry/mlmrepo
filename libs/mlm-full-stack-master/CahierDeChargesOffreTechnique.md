# Conception de la plateforme web/Siteweb pour gestion de tontine collective

## Contexte

Le client souhaite mettre en place une plateforme de tontine collectif dynamique et moderne en vue de promouvoir l'indépendance économique des évetuels membres du réseau.
L'objectif de la plateforme serait de donner un nouveau souffle numérique aux activités du client, en vue d'optimiser, d'automatiser et de dynamiser:

- L'adhésion des utilisateurs sur la plateforme
- la collecte des éventuelle frais de tontine ou d'adhésion
- La suivie de l'évolution du réseau des membres
- Le reversement/paiement des gains éventuel des membre sur la plateforme
- La communication
- Le suivie des membres du système.

C'est dans ce cardre que nous intervenons dans la mise en oeuvre de l'aspect digital de cette plateforme, et en sens, le présent document propose la méthodologie de mise en place de l'aspect numérique du projet en incluant l'essentiel des fonctionnalités souhaités par le client.

## Cahier de charge des fonctionnalités

Cette section du document présent une vue détaillé des fonctionnalités devant être implémenter par notre partie en vue d'une mise en place effective de la plateforme:

### Adhésion des utilisateurs à la tontine

Pour bénéficier des avantages d'un réseau, tout utilisateur doit au préable soumettre ces informations via un formulaire d'enrégistrement mise a dispotion dans  un espace d'inscription sur la plateforme.

Les internautes pour finaliser leur inscription seront redirigés sur un espace de paiement, si n'ayant pas fourni de code de paiement sur le forumulaire d'enrégistrement, pour la confirmation de leur inscription.

Tout utilisateur ainsi enrégistrer devient member et dispose d'un portefeuille électronique de gain, suite à la validation de son inscription par les administrateurs du système.

#### Gestion des groupes

Un groupe est une entité permettant de regrouper les action pouvant être effectuer par un utilisateur du système. Il se présente comme étant efficace dans une gestion plus optimale de la sécurité des données du système (un membre inscrit via le formulaire d'adhésion, ne disposera pas des même privilèges qu'un administrateur du système, de même au sein de l'administration nous pourront disposer des niveau d'abilitation différent).

### Espace des utilisateur

Un espace membre sera mise en place pour chaque membre dont l'adhésion aura été validé par le(s) administrateur(s), leur permettant de faire une suivi des opération effectués sur leur portefeuille virtuelle, de suivre l'évolution de leur réseau, et d'automitiser les éventuelles virements sur le compte de leur choix.

En parallèle, les administrateurs du système disposeront de leur espace de travaille autre que l'espace des membres, où, ils pourront faire une suivie globale de la plateforme, de publier les informations à propos des programmes et événements, revoquer les accès utilisateurs et définir certaines configurations éventuelles du système.

### Espace grand public

Cet espace sera comme son nom l'indique réservé au public et devra présenter l'entreprise dans sa globalités, les activités de collecte et les publication d'événements de formation, d'assistance en coaching et des programmes (conférences & atelier) qu'aura a effectué l'entreprise.
L'espace grand public servira non seulement d'espace vitrine pour l'entreprise mais de aussi des informations nécessaire d'adhésion et d'une foire aux questions pour permettre aux internautes d'avoir plus détails sur le système de tontine et ses avantages.

### Gestion des gain et des paiements

Les membres dont l'adhésion a été validé par les administrateur disposeront d'un portefeuille de gain qui est approvisionné par l'adhésion de leur fieuls selon l'algorithme défini par le client.
Les membre pourront ainsi decider de retirer leur gains et de les convertir en valeur physique. Cette opération fait intervenir les moyens de paiement manuelle (Main à main), mobile (ex: Tmoney, Flooz), ou via des transfert d'argent.

NB: Les détails de cette gestion présenté ici sont superficiel, et seront plus dévéloppé lors des phases de développement de la plateforme.

### Comminication (Mail, SMS, Newsletters)

- Les newsletters sont un  des moyens de communication consistant à envoyer des informations par courrier électronique à une liste de destinataires. Ils permettront de notifier les members de éventuels pulication des ateliers de formation, des conférences, etc...

- D'autre part la plateforme devra disposer d'un service de mailing pour l'envoie de courriel électronique à la suite des processus d'adhésion, de paiement de gain, etc...

- Un autre service de messagerie via SMS, pour l'envoi de notification de confirmation de certaines opérations sur la plateforme, tels que, les confirmation de transaction de paiement, etc...

### Hébergement et nom de domaine

- L’évolution du web prend en compte la prise en charge de domaine liées à l’activité et cadrant sémantiquement avec l’entreprise. Nous proposons un nom de domaine devant convenir à la raison sociale de l'entreprise et qui deviendra le nouveau visage de l'entreprise.

- Dans le but de rendre accessible le site en mode sécurisé via un protocole HTTPS un certificat SSL sera acquis et installé. Ce certificat SSL assure le chiffrement et l'intégrité des données.

- L'hébergement sera fait sur un serveur virtuel en ligne disposant de son propre espace, limitant les risque de corruption de données et une fluidité des échanges et des communication client-serveur, ce que n'offre pas les hébergements partagés.
