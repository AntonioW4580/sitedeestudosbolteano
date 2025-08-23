-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23/08/2025 às 03:02
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `meusite`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `subtopic_id` int(11) NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `texto` text DEFAULT NULL,
  `capa` varchar(500) DEFAULT NULL,
  `imagens` text DEFAULT NULL,
  `links` text DEFAULT NULL,
  `hashtags` text DEFAULT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `posts`
--

INSERT INTO `posts` (`id`, `subtopic_id`, `titulo`, `descricao`, `texto`, `capa`, `imagens`, `links`, `hashtags`, `data_criacao`) VALUES
(112, 127, 'AA', 'AA', 'AA', '', 'a:0:{}', 'a:0:{}', 'a:0:{}', '2025-08-21 20:45:15'),
(113, 118, 'a', 'a', 'a', '', 'a:0:{}', 'a:0:{}', 'a:0:{}', '2025-08-21 23:48:02');

-- --------------------------------------------------------

--
-- Estrutura para tabela `posts_questoes`
--

CREATE TABLE `posts_questoes` (
  `id` int(11) NOT NULL,
  `topico_secundario_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `conteudo` text DEFAULT NULL,
  `nivel_dificuldade` enum('fácil','médio','difícil','muito difícil') DEFAULT 'médio',
  `status` enum('acertou','errou') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `questoes`
--

CREATE TABLE `questoes` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `questoes`
--

INSERT INTO `questoes` (`id`, `nome`, `descricao`, `created_at`, `updated_at`) VALUES
(20, '📚 Português', NULL, '2025-08-22 22:50:17', '2025-08-22 22:50:17'),
(21, '➗ Matemática', NULL, '2025-08-22 22:50:17', '2025-08-22 22:50:17'),
(22, '⚛️ Física', NULL, '2025-08-22 22:50:17', '2025-08-22 22:50:17'),
(23, '🧪 Química', NULL, '2025-08-22 22:50:17', '2025-08-22 22:50:17'),
(24, '🧬 Biologia', NULL, '2025-08-22 22:50:17', '2025-08-22 22:50:17'),
(25, '🏛️ História', NULL, '2025-08-22 22:50:17', '2025-08-22 22:50:17'),
(26, '🌍 Geografia', NULL, '2025-08-22 22:50:17', '2025-08-22 22:50:17'),
(27, '📖 Literatura', NULL, '2025-08-22 22:50:17', '2025-08-22 22:50:17'),
(28, '🇬🇧 Inglês', NULL, '2025-08-22 22:50:17', '2025-08-22 22:50:17'),
(29, '💭 Filosofia', NULL, '2025-08-22 22:50:17', '2025-08-22 22:50:17'),
(30, '👥 Sociologia', NULL, '2025-08-22 22:50:17', '2025-08-22 22:50:17');

-- --------------------------------------------------------

--
-- Estrutura para tabela `subtopics`
--

CREATE TABLE `subtopics` (
  `id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `capa` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `subtopics`
--

INSERT INTO `subtopics` (`id`, `topic_id`, `nome`, `descricao`, `created_at`, `capa`) VALUES
(73, 35, 'Matemática Básica', '', '2025-08-20 19:01:31', 'uploads/68a61b8b86fc6.jpg'),
(74, 35, 'Funções', '', '2025-08-20 19:02:33', 'uploads/68a61be8180b6.webp'),
(75, 35, 'Geometria Plana', '', '2025-08-20 19:03:49', 'uploads/68a61c15d4ad7.png'),
(77, 35, 'Sequências (PA e PG)', '', '2025-08-20 19:10:26', 'uploads/68a61da2cfce0.png'),
(78, 35, 'Probabilidade', '', '2025-08-20 19:19:59', 'uploads/68a61fdfc96a4.webp'),
(79, 35, 'Matrizes e Determinantes', '', '2025-08-20 19:20:52', 'uploads/68a62014f184b.png'),
(80, 35, 'Análise Combinatória', '', '2025-08-20 19:21:58', 'uploads/68a620569d323.webp'),
(81, 35, 'Geometria Espacial', '', '2025-08-20 19:22:51', 'uploads/68a6208bd134a.png'),
(82, 35, 'Geometria Analítica', '', '2025-08-20 19:27:42', 'uploads/68a621aed5c10.png'),
(83, 35, 'Matemática Financeira', '', '2025-08-20 19:28:35', 'uploads/68a621e3edbf5.jpg'),
(84, 35, 'Números Complexos', '', '2025-08-20 19:29:34', 'uploads/68a6221eb609c.webp'),
(85, 35, 'Trigonometria', '', '2025-08-20 19:30:15', 'uploads/68a62247c9f8f.jpg'),
(86, 35, 'Sistemas Lineares', '', '2025-08-20 19:38:28', 'uploads/68a624345a34d.webp'),
(87, 35, 'Estatística', '', '2025-08-20 19:40:12', 'uploads/68a6249c971d3.webp'),
(88, 41, 'Introdução à Física e Seus Fundamentos', '', '2025-08-20 19:46:49', 'uploads/68a626292c0d3.webp'),
(89, 41, 'Cinemática', '', '2025-08-20 19:47:24', 'uploads/68a6264c720f9.webp'),
(90, 41, 'Dinâmica', '', '2025-08-20 19:49:29', 'uploads/68a626c94d99d.png'),
(91, 41, 'Estática', '', '2025-08-20 19:51:44', 'uploads/68a62750c8ed2.png'),
(92, 41, 'Gravitação Universal', '', '2025-08-20 19:52:02', 'uploads/68a62762d4f4a.webp'),
(93, 41, 'Hidrostática', '', '2025-08-20 19:52:45', 'uploads/68a6278d814db.webp'),
(94, 41, 'Termologia', '', '2025-08-20 19:53:50', 'uploads/68a627ce9082a.webp'),
(95, 41, 'Ondulatória', '', '2025-08-20 19:54:25', 'uploads/68a627f162e03.webp'),
(96, 41, 'Óptica', '', '2025-08-20 19:54:53', 'uploads/68a6280d5d7f5.jpg'),
(97, 41, 'Física Moderna', '', '2025-08-20 19:55:33', 'uploads/68a62835b9755.jpg'),
(98, 41, 'Eletrostática', '', '2025-08-20 19:56:52', 'uploads/68a62884668e2.webp'),
(99, 41, 'Eletrodinâmica', '', '2025-08-20 19:57:25', 'uploads/68a628a5b6bcb.webp'),
(100, 41, 'Eletromagnetismo', '', '2025-08-20 19:57:55', 'uploads/68a628c3388d3.jpg'),
(102, 42, 'Introdução A Química', '', '2025-08-20 20:00:42', 'uploads/68a6296a29006.webp'),
(103, 42, 'Estrutura Atômica', '', '2025-08-20 20:03:07', 'uploads/68a629fb4d4c6.webp'),
(104, 42, 'Classificação Periódica dos Elementos (Tabela Periódica)', '', '2025-08-20 20:03:44', 'uploads/68a62a20f1209.webp'),
(105, 42, 'Ligação Química e Geometria Molecular', '', '2025-08-20 20:05:08', 'uploads/68a62a74dfad4.png'),
(106, 42, 'Funções Inorgânicas', '', '2025-08-20 20:05:46', 'uploads/68a62a9aa827b.webp'),
(107, 42, 'Reações Químicas e Estequiometria', '', '2025-08-20 20:06:27', 'uploads/68a62ac35d617.webp'),
(108, 42, 'Gases', '', '2025-08-20 20:06:58', 'uploads/68a62ae27a787.webp'),
(109, 42, 'Termoquímica', '', '2025-08-20 20:08:26', 'uploads/68a62b3a7f943.jpg'),
(110, 42, 'Cinética Química', '', '2025-08-20 20:08:50', 'uploads/68a62b52c2380.webp'),
(111, 42, 'Equilíbrio Químico', '', '2025-08-20 20:09:31', 'uploads/68a62b7b44e7a.webp'),
(112, 42, 'Eletroquímica', '', '2025-08-20 20:09:57', 'uploads/68a62b954ec0b.jpg'),
(113, 42, 'Química Orgânica', '', '2025-08-20 20:10:19', 'uploads/68a62babd11d7.jpg'),
(114, 42, 'Radioatividade e Química Nuclear', '', '2025-08-20 20:10:42', 'uploads/68a62bc21de39.webp'),
(115, 42, 'Solubilidade e Propriedades Coligativas', '', '2025-08-20 20:14:04', 'uploads/68a62c8c55000.webp'),
(116, 42, 'Materiais e suas Propriedades (Química Geral)', '', '2025-08-20 20:14:33', 'uploads/68a62ca97425c.webp'),
(117, 43, 'Introdução à Biologia e Origem da Vida', '', '2025-08-20 20:42:38', 'uploads/68a6333e2d267.jpg'),
(118, 43, 'Citologia', '', '2025-08-20 20:43:03', 'uploads/68a63357109c1.jpg'),
(119, 43, 'Histologia', '', '2025-08-20 20:43:26', 'uploads/68a6336ec60fe.webp'),
(120, 43, 'Bioquímica', '', '2025-08-20 20:43:52', 'uploads/68a63388c8af5.webp'),
(121, 43, 'Genética e Biologia Molecular', '', '2025-08-20 20:44:18', 'uploads/68a633a2cea66.webp'),
(122, 43, 'Evolução Biológica', '', '2025-08-20 20:44:43', 'uploads/68a633bbcce0a.png'),
(123, 43, 'Classificação dos Seres Vivos (Taxonomia)', '', '2025-08-20 20:45:13', 'uploads/68a633d9da9aa.webp'),
(124, 43, 'Reinos: Vírus, Bactérias, Protistas e Fungos', '', '2025-08-20 20:45:40', 'uploads/68a633f4252f6.webp'),
(125, 43, 'Reino Plantae', '', '2025-08-20 20:46:05', 'uploads/68a6340dc1b43.webp'),
(126, 43, 'Reino Animalia', '', '2025-08-20 20:46:34', 'uploads/68a6342a4f4de.webp'),
(127, 43, 'Anatomia e Fisiologia Humana', '', '2025-08-20 20:47:29', 'uploads/68a634611f79f.webp'),
(128, 43, 'Saúde e Doenças (Imunologia, Epidemias, Zoonoses)', '', '2025-08-20 20:48:06', 'uploads/68a63486f2222.jpg'),
(129, 43, 'Parasitologia', '', '2025-08-20 20:48:26', 'uploads/68a6349ab6500.webp'),
(130, 43, 'Biotecnologia', '', '2025-08-20 20:48:43', 'uploads/68a634abd1d4d.jpg'),
(131, 43, 'Ecologia', '', '2025-08-20 20:49:02', 'uploads/68a634be5afb6.webp'),
(132, 36, 'Introdução ao estudo da Linguagem', '', '2025-08-20 21:54:20', 'uploads/68a6440c83c66.webp'),
(133, 36, 'Fonética e Fonologia', '', '2025-08-20 21:54:59', 'uploads/68a6443353760.webp'),
(134, 36, 'Morfologia', '', '2025-08-20 21:55:53', 'uploads/68a64469bf529.png'),
(135, 36, 'Sintaxe', '', '2025-08-20 21:56:16', 'uploads/68a644805e6d7.webp'),
(136, 36, 'Semântica e Estilística', '', '2025-08-20 21:57:00', 'uploads/68a644ac1dcad.webp'),
(137, 36, 'Pontuação e Acentuação.', '', '2025-08-20 22:20:59', 'uploads/68a64a4b2b042.webp'),
(138, 36, 'Interpretação e Compreensão de Texto', '', '2025-08-20 22:21:34', 'uploads/68a64a6eedd3f.webp'),
(139, 36, 'Gêneros Textuais', '', '2025-08-20 22:21:54', 'uploads/68a64a8205b61.webp'),
(141, 36, 'Variação Linguística', '', '2025-08-20 22:22:57', 'uploads/68a64ac1bc42b.png'),
(142, 36, 'Norma Culta vs. Norma Popular', '', '2025-08-20 22:23:25', 'uploads/68a64add8a427.webp'),
(143, 36, 'Figuras de Linguagem', '', '2025-08-20 22:23:43', 'uploads/68a64aef97cce.jpg'),
(144, 36, 'Funções da Linguagem', '', '2025-08-20 22:24:19', 'uploads/68a64b13ce681.png'),
(145, 36, 'Coesão e Coerência', '', '2025-08-20 22:24:39', 'uploads/68a64b27ee600.png'),
(146, 36, 'Redação e Produção Textual', '', '2025-08-20 22:25:34', 'uploads/68a64b5e99872.png'),
(147, 37, 'Introdução ao Estudo da História', '', '2025-08-20 23:23:53', 'uploads/68a6590920e14.webp'),
(148, 37, 'Pré-História e Antiguidade Oriental', '', '2025-08-20 23:25:37', 'uploads/68a659717d710.jpg'),
(150, 37, 'Antiguidade Clássica', '', '2025-08-20 23:26:27', 'uploads/68a659b556872.webp'),
(151, 37, 'Idade Média', '', '2025-08-20 23:27:01', 'uploads/68a659c51842f.webp'),
(152, 37, 'Idade Moderna', '', '2025-08-20 23:27:31', 'uploads/68a659e339d74.webp'),
(153, 37, 'Idade Contemporânea', '', '2025-08-20 23:27:53', 'uploads/68a659f9304d3.webp'),
(154, 37, 'Era das Revoluções', '', '2025-08-20 23:28:40', 'uploads/68a65a281853b.jpg'),
(155, 37, 'Século XIX', '', '2025-08-20 23:29:23', 'uploads/68a65a53c0063.webp'),
(156, 37, 'Século XX e Mundo Contemporâneo', '', '2025-08-20 23:30:06', 'uploads/68a65a7ec7bce.jpg'),
(157, 37, 'História do Brasil Colônia', '', '2025-08-20 23:36:08', 'uploads/68a65be8dbe3c.webp'),
(158, 37, 'História do Brasil Império', '', '2025-08-20 23:36:33', 'uploads/68a65c0133143.jpg'),
(159, 37, 'História do Brasil República', '', '2025-08-20 23:37:11', 'uploads/68a65c27c5855.webp'),
(160, 40, 'Introdução à Geografia', '', '2025-08-20 23:47:47', 'uploads/68a65ea327b95.webp'),
(161, 40, 'Cartografia', '', '2025-08-20 23:48:09', 'uploads/68a65eb9ad099.jpg'),
(162, 40, 'Geologia e Relevo', '', '2025-08-20 23:49:08', 'uploads/68a65ef4096a5.jpg'),
(163, 40, 'Climatologia', '', '2025-08-20 23:49:26', 'uploads/68a65f06577e0.jpg'),
(164, 40, 'Hidrografia', '', '2025-08-20 23:49:49', 'uploads/68a65f1d19324.webp'),
(165, 40, 'Biogeografia', '', '2025-08-20 23:50:09', 'uploads/68a65f311533e.jpg'),
(166, 40, 'Geografia da População', '', '2025-08-20 23:50:32', 'uploads/68a65f486ac90.jpg'),
(167, 40, 'Geografia Urbana', '', '2025-08-20 23:50:59', 'uploads/68a65f6316bf7.png'),
(168, 40, 'Geografia Agrária', '', '2025-08-20 23:51:25', 'uploads/68a65f7ded35f.webp'),
(169, 40, 'Geografia Econômica', '', '2025-08-20 23:51:42', 'uploads/68a65f8eb9510.webp'),
(170, 40, 'Geopolítica', '', '2025-08-20 23:52:05', 'uploads/68a65fa5d1284.webp'),
(171, 40, 'Geografia do Brasil', '', '2025-08-20 23:52:33', 'uploads/68a65fc18006a.webp'),
(172, 38, 'Introdução à Filosofia', '', '2025-08-21 01:32:07', 'uploads/68a6771787d27.png'),
(173, 38, 'Filosofia Antiga', '', '2025-08-21 01:32:25', 'uploads/68a677297807a.webp'),
(174, 38, 'Filosofia Medieval', '', '2025-08-21 01:32:46', 'uploads/68a6773e358f9.jpg'),
(175, 38, 'Filosofia Moderna', '', '2025-08-21 01:33:14', 'uploads/68a6775a2dfc6.webp'),
(176, 38, 'Filosofia Contemporânea', '', '2025-08-21 01:33:41', 'uploads/68a6777564182.webp'),
(177, 38, 'Ética e Filosofia Moral', '', '2025-08-21 01:34:03', 'uploads/68a6778b1e05f.jpg'),
(178, 38, 'Filosofia Política', '', '2025-08-21 01:34:24', 'uploads/68a677a0a7a28.webp'),
(179, 38, 'Teoria do Conhecimento (Epistemologia)', '', '2025-08-21 01:35:44', 'uploads/68a677f05f744.jpg'),
(180, 38, 'Filosofia da Ciência (Método)', '', '2025-08-21 01:36:17', 'uploads/68a6781107705.jpg'),
(181, 38, 'Filosofia da Linguagem', '', '2025-08-21 01:36:45', 'uploads/68a6782d697b4.jpg'),
(182, 38, 'Estética', '', '2025-08-21 01:37:19', 'uploads/68a6784f523c8.webp'),
(183, 38, 'Filosofia Brasileira e Outras Tradições', '', '2025-08-21 01:37:52', 'uploads/68a67870d4698.jpg'),
(184, 39, 'Introdução à Sociologia', '', '2025-08-21 01:45:50', 'uploads/68a67a4ec6c0e.jpg'),
(185, 39, 'Os Clássicos da Sociologia', '', '2025-08-21 01:46:25', 'uploads/68a67a71d025c.webp'),
(186, 39, 'Estratificação e Desigualdade Social', '', '2025-08-21 01:47:53', 'uploads/68a67ac9bd3ed.webp'),
(187, 39, 'Cultura e Ideologia', '', '2025-08-21 01:48:13', 'uploads/68a67add1c2ea.jpg'),
(188, 39, 'Poder, Estado e Política', '', '2025-08-21 01:48:54', 'uploads/68a67b06c2234.png'),
(189, 39, 'Trabalho e Sociologia Econômica', '', '2025-08-21 01:49:47', 'uploads/68a67b3bacb0b.jpg'),
(190, 39, 'Educação e Sociedade', '', '2025-08-21 01:50:07', 'uploads/68a67b4f056fc.webp'),
(191, 39, 'Gênero, Raça e Etnia', '', '2025-08-21 01:50:29', 'uploads/68a67b65cf758.jpg'),
(192, 39, 'Juventude e Cultura Urbana', '', '2025-08-21 01:51:01', 'uploads/68a67b858505d.jpg'),
(193, 39, 'Sociologia Brasileira', '', '2025-08-21 01:51:26', 'uploads/68a67b9e8583a.jpg'),
(194, 45, 'Literatura Para Vestibulares', '', '2025-08-21 02:02:34', 'uploads/68a67e3a553d0.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `topicos_secundarios`
--

CREATE TABLE `topicos_secundarios` (
  `id` int(11) NOT NULL,
  `questao_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `topicos_secundarios`
--

INSERT INTO `topicos_secundarios` (`id`, `questao_id`, `nome`, `descricao`, `created_at`, `updated_at`) VALUES
(7, 22, 'Cinemática', '', '2025-08-22 22:53:44', '2025-08-22 22:53:44'),
(8, 22, '📌 Cinemática', NULL, '2025-08-22 23:03:12', '2025-08-22 23:03:12'),
(9, 22, '⚖️ Leis de Newton', NULL, '2025-08-22 23:03:12', '2025-08-22 23:03:12'),
(10, 22, '🔄 Trabalho, Energia e Potência', NULL, '2025-08-22 23:03:12', '2025-08-22 23:03:12'),
(11, 22, '💥 Quantidade de Movimento e Impulso', NULL, '2025-08-22 23:03:12', '2025-08-22 23:03:12'),
(12, 22, '🌡️ Termologia e Termodinâmica', NULL, '2025-08-22 23:03:12', '2025-08-22 23:03:12'),
(13, 22, '🌊 Ondulatória', NULL, '2025-08-22 23:03:12', '2025-08-22 23:03:12'),
(14, 22, '⚡ Eletrostática e Eletrodinâmica', NULL, '2025-08-22 23:03:12', '2025-08-22 23:03:12'),
(15, 22, '🧲 Magnetismo e Eletromagnetismo', NULL, '2025-08-22 23:03:12', '2025-08-22 23:03:12'),
(16, 22, '🔋 Eletricidade e Circuitos', NULL, '2025-08-22 23:03:12', '2025-08-22 23:03:12'),
(17, 22, '⚛️ Física Moderna', NULL, '2025-08-22 23:03:12', '2025-08-22 23:03:12'),
(18, 23, '🧪 Estrutura Atômica', NULL, '2025-08-22 23:08:45', '2025-08-22 23:08:45'),
(19, 23, '🧱 Tabela Periódica', NULL, '2025-08-22 23:08:45', '2025-08-22 23:08:45'),
(20, 23, '🔗 Ligações Químicas', NULL, '2025-08-22 23:08:45', '2025-08-22 23:08:45'),
(21, 23, '⚖️ Estequiometria', NULL, '2025-08-22 23:08:45', '2025-08-22 23:08:45'),
(22, 23, '🌡️ Termoquímica', NULL, '2025-08-22 23:08:45', '2025-08-22 23:08:45'),
(23, 23, '⚡ Cinética Química', NULL, '2025-08-22 23:08:45', '2025-08-22 23:08:45'),
(24, 23, '🔁 Equilíbrio Químico', NULL, '2025-08-22 23:08:45', '2025-08-22 23:08:45'),
(25, 23, '💧 Soluções', NULL, '2025-08-22 23:08:45', '2025-08-22 23:08:45'),
(26, 23, '🔋 Eletroquímica', NULL, '2025-08-22 23:08:45', '2025-08-22 23:08:45'),
(27, 23, '🌿 Química Orgânica', NULL, '2025-08-22 23:08:45', '2025-08-22 23:08:45'),
(28, 24, '🔬 Citologia', NULL, '2025-08-22 23:09:25', '2025-08-22 23:09:25'),
(29, 24, '🧫 Microbiologia', NULL, '2025-08-22 23:09:25', '2025-08-22 23:09:25'),
(30, 24, '🧬 Genética', NULL, '2025-08-22 23:09:25', '2025-08-22 23:09:25'),
(31, 24, '🧍 Evolução', NULL, '2025-08-22 23:09:25', '2025-08-22 23:09:25'),
(32, 24, '🌳 Botânica', NULL, '2025-08-22 23:09:25', '2025-08-22 23:09:25'),
(33, 24, '🐾 Zoologia', NULL, '2025-08-22 23:09:25', '2025-08-22 23:09:25'),
(34, 24, '🧠 Fisiologia Humana', NULL, '2025-08-22 23:09:25', '2025-08-22 23:09:25'),
(35, 24, '🌍 Ecologia', NULL, '2025-08-22 23:09:25', '2025-08-22 23:09:25'),
(36, 24, '🧼 Bioquímica', NULL, '2025-08-22 23:09:25', '2025-08-22 23:09:25'),
(37, 24, '🧬 Biotecnologia', NULL, '2025-08-22 23:09:25', '2025-08-22 23:09:25'),
(38, 25, '🏺 Antiguidade Clássica', NULL, '2025-08-22 23:09:40', '2025-08-22 23:09:40'),
(39, 25, '⚔️ Idade Média', NULL, '2025-08-22 23:09:40', '2025-08-22 23:09:40'),
(40, 25, '🎨 Renascimento e Reformas', NULL, '2025-08-22 23:09:40', '2025-08-22 23:09:40'),
(41, 25, '🚀 Expansão Marítima', NULL, '2025-08-22 23:09:40', '2025-08-22 23:09:40'),
(42, 25, '🏭 Revoluções Industriais', NULL, '2025-08-22 23:09:40', '2025-08-22 23:09:40'),
(43, 25, '🇧🇷 Brasil Colônia', NULL, '2025-08-22 23:09:40', '2025-08-22 23:09:40'),
(44, 25, '🇧🇷 Brasil Império', NULL, '2025-08-22 23:09:40', '2025-08-22 23:09:40'),
(45, 25, '🇧🇷 Brasil República', NULL, '2025-08-22 23:09:40', '2025-08-22 23:09:40'),
(46, 25, '💣 1ª e 2ª Guerra Mundial', NULL, '2025-08-22 23:09:40', '2025-08-22 23:09:40'),
(47, 25, '🧊 Guerra Fria', NULL, '2025-08-22 23:09:40', '2025-08-22 23:09:40'),
(48, 26, '🗺️ Cartografia', NULL, '2025-08-22 23:09:49', '2025-08-22 23:09:49'),
(49, 26, '⛰️ Geografia Física', NULL, '2025-08-22 23:09:49', '2025-08-22 23:09:49'),
(50, 26, '🏙️ Geografia Urbana', NULL, '2025-08-22 23:09:49', '2025-08-22 23:09:49'),
(51, 26, '🌾 Geografia Agrária', NULL, '2025-08-22 23:09:49', '2025-08-22 23:09:49'),
(52, 26, '🏭 Geografia Industrial', NULL, '2025-08-22 23:09:49', '2025-08-22 23:09:49'),
(53, 26, '🇧🇷 Brasil: Regiões e Economia', NULL, '2025-08-22 23:09:49', '2025-08-22 23:09:49'),
(54, 26, '🌐 Globalização', NULL, '2025-08-22 23:09:49', '2025-08-22 23:09:49'),
(55, 26, '🔥 Mudanças Climáticas', NULL, '2025-08-22 23:09:49', '2025-08-22 23:09:49'),
(56, 26, '👥 População e Demografia', NULL, '2025-08-22 23:09:49', '2025-08-22 23:09:49'),
(57, 26, '⛏️ Recursos Naturais', NULL, '2025-08-22 23:09:49', '2025-08-22 23:09:49'),
(58, 20, '🔤 Gramática: Classes Gramaticais', NULL, '2025-08-22 23:09:57', '2025-08-22 23:09:57'),
(59, 20, '✍️ Regência e Concordância', NULL, '2025-08-22 23:09:57', '2025-08-22 23:09:57'),
(60, 20, '🔗 Colocação Pronominal', NULL, '2025-08-22 23:09:57', '2025-08-22 23:09:57'),
(61, 20, '📖 Interpretação de Texto', NULL, '2025-08-22 23:09:57', '2025-08-22 23:09:57'),
(62, 20, '🎯 Figuras de Linguagem', NULL, '2025-08-22 23:09:57', '2025-08-22 23:09:57'),
(63, 20, '📜 Tipologia Textual', NULL, '2025-08-22 23:09:57', '2025-08-22 23:09:57'),
(64, 20, '📜 Semântica e Significação', NULL, '2025-08-22 23:09:57', '2025-08-22 23:09:57'),
(65, 20, '💬 Fonética e Fonologia', NULL, '2025-08-22 23:09:57', '2025-08-22 23:09:57'),
(66, 20, '📝 Pontuação', NULL, '2025-08-22 23:09:57', '2025-08-22 23:09:57'),
(67, 20, '📚 Literatura Brasileira', NULL, '2025-08-22 23:09:57', '2025-08-22 23:09:57'),
(68, 27, '📚 Arcadismo', NULL, '2025-08-22 23:10:06', '2025-08-22 23:10:06'),
(69, 27, '🔥 Romantismo', NULL, '2025-08-22 23:10:06', '2025-08-22 23:10:06'),
(70, 27, '🔍 Realismo/Naturalismo', NULL, '2025-08-22 23:10:06', '2025-08-22 23:10:06'),
(71, 27, '🎨 Parnasianismo', NULL, '2025-08-22 23:10:06', '2025-08-22 23:10:06'),
(72, 27, '🌀 Simbolismo', NULL, '2025-08-22 23:10:06', '2025-08-22 23:10:06'),
(73, 27, '💥 Pré-Modernismo', NULL, '2025-08-22 23:10:06', '2025-08-22 23:10:06'),
(74, 27, '🚀 Modernismo', NULL, '2025-08-22 23:10:06', '2025-08-22 23:10:06'),
(75, 27, '🕊️ Geração de 60/Contemporânea', NULL, '2025-08-22 23:10:06', '2025-08-22 23:10:06'),
(76, 27, '📜 Literatura Clássica (Grécia e Roma)', NULL, '2025-08-22 23:10:06', '2025-08-22 23:10:06'),
(77, 27, '📖 Literatura Portuguesa', NULL, '2025-08-22 23:10:06', '2025-08-22 23:10:06'),
(78, 28, '🔤 Vocabulário Básico', NULL, '2025-08-22 23:10:17', '2025-08-22 23:10:17'),
(79, 28, '📚 Presente Simples e Contínuo', NULL, '2025-08-22 23:10:17', '2025-08-22 23:10:17'),
(80, 28, '📅 Passado Simples e Contínuo', NULL, '2025-08-22 23:10:17', '2025-08-22 23:10:17'),
(81, 28, '🔮 Futuro (will, going to)', NULL, '2025-08-22 23:10:17', '2025-08-22 23:10:17'),
(82, 28, '🎯 Modal Verbs (can, must, should)', NULL, '2025-08-22 23:10:17', '2025-08-22 23:10:17'),
(83, 28, '🔗 Conditionals (If Clauses)', NULL, '2025-08-22 23:10:17', '2025-08-22 23:10:17'),
(84, 28, '📖 Reported Speech', NULL, '2025-08-22 23:10:17', '2025-08-22 23:10:17'),
(85, 28, '🔍 Phrasal Verbs', NULL, '2025-08-22 23:10:17', '2025-08-22 23:10:17'),
(86, 28, '💬 Prepositions of Time and Place', NULL, '2025-08-22 23:10:17', '2025-08-22 23:10:17'),
(87, 28, '📚 Interpretação de Texto em Inglês', NULL, '2025-08-22 23:10:17', '2025-08-22 23:10:17'),
(88, 29, '🏛️ Filosofia Antiga (Grécia)', NULL, '2025-08-22 23:10:30', '2025-08-22 23:10:30'),
(89, 29, '⛪ Filosofia Medieval', NULL, '2025-08-22 23:10:30', '2025-08-22 23:10:30'),
(90, 29, '💡 Iluminismo', NULL, '2025-08-22 23:10:30', '2025-08-22 23:10:30'),
(91, 29, '🧠 Racionalismo e Empirismo', NULL, '2025-08-22 23:10:30', '2025-08-22 23:10:30'),
(92, 29, '⚖️ Ética e Moral', NULL, '2025-08-22 23:10:30', '2025-08-22 23:10:30'),
(93, 29, '💭 Existencialismo', NULL, '2025-08-22 23:10:30', '2025-08-22 23:10:30'),
(94, 29, '🔍 Lógica Formal', NULL, '2025-08-22 23:10:30', '2025-08-22 23:10:30'),
(95, 29, '🌍 Filosofia Política', NULL, '2025-08-22 23:10:30', '2025-08-22 23:10:30'),
(96, 29, '👁️ Teoria do Conhecimento', NULL, '2025-08-22 23:10:30', '2025-08-22 23:10:30'),
(97, 29, '🕊️ Filosofia Contemporânea', NULL, '2025-08-22 23:10:30', '2025-08-22 23:10:30'),
(98, 30, '👥 Introdução à Sociologia', NULL, '2025-08-22 23:10:39', '2025-08-22 23:10:39'),
(99, 30, '🏛️ Teorias Sociológicas (Durkheim, Marx, Weber)', NULL, '2025-08-22 23:10:39', '2025-08-22 23:10:39'),
(100, 30, '🏢 Estrutura Social', NULL, '2025-08-22 23:10:39', '2025-08-22 23:10:39'),
(101, 30, '🏙️ Meio Urbano e Rural', NULL, '2025-08-22 23:10:39', '2025-08-22 23:10:39'),
(102, 30, '💼 Trabalho e Sociedade', NULL, '2025-08-22 23:10:39', '2025-08-22 23:10:39'),
(103, 30, '⚖️ Desigualdade Social', NULL, '2025-08-22 23:10:39', '2025-08-22 23:10:39'),
(104, 30, '🛡️ Movimentos Sociais', NULL, '2025-08-22 23:10:39', '2025-08-22 23:10:39'),
(105, 30, '🌐 Globalização e Cultura', NULL, '2025-08-22 23:10:39', '2025-08-22 23:10:39'),
(106, 30, '🏛️ Estado, Poder e Política', NULL, '2025-08-22 23:10:39', '2025-08-22 23:10:39'),
(107, 30, '🎯 Identidade e Diversidade', NULL, '2025-08-22 23:10:39', '2025-08-22 23:10:39'),
(108, 21, '➕ Conjuntos e Conjuntos Numéricos', NULL, '2025-08-22 23:11:07', '2025-08-22 23:11:07'),
(109, 21, '📊 Estatística e Probabilidade', NULL, '2025-08-22 23:11:07', '2025-08-22 23:11:07'),
(110, 21, '📊 Análise Combinatória', NULL, '2025-08-22 23:11:07', '2025-08-22 23:11:07'),
(111, 21, '📈 Funções (1º e 2º grau)', NULL, '2025-08-22 23:11:07', '2025-08-22 23:11:07'),
(112, 21, '📐 Trigonometria', NULL, '2025-08-22 23:11:07', '2025-08-22 23:11:07'),
(113, 21, '📐 Geometria Plana', NULL, '2025-08-22 23:11:07', '2025-08-22 23:11:07'),
(114, 21, '🔷 Geometria Espacial', NULL, '2025-08-22 23:11:07', '2025-08-22 23:11:07'),
(115, 21, '🌐 Geometria Analítica', NULL, '2025-08-22 23:11:07', '2025-08-22 23:11:07'),
(116, 21, '🔢 Logaritmos e Exponenciais', NULL, '2025-08-22 23:11:07', '2025-08-22 23:11:07'),
(117, 21, '📊 Matemática Financeira', NULL, '2025-08-22 23:11:07', '2025-08-22 23:11:07'),
(118, 20, 'Introdução a Linguagem Portuguesa ✍️', '', '2025-08-23 00:58:34', '2025-08-23 00:58:34');

-- --------------------------------------------------------

--
-- Estrutura para tabela `topics`
--

CREATE TABLE `topics` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `topics`
--

INSERT INTO `topics` (`id`, `nome`, `descricao`, `created_at`) VALUES
(35, 'Matemática 📐', 'Estudo dos números.', '2025-08-20 03:36:59'),
(36, 'Português 🔠', 'Língua portuguesa e seus tópicos.', '2025-08-20 03:38:11'),
(37, 'História 🗿', 'Estudo do passado no tempo-espaço.', '2025-08-20 03:39:07'),
(38, 'Filosofia 🤔', 'Estudo do conhecimento, realidade, moralidade e da existência.', '2025-08-20 03:40:15'),
(39, 'Sociologia 🏙️', 'Estudo científico da sociedade.', '2025-08-20 03:41:33'),
(40, 'Geografia 🌐', 'Estudo dos espaços geográficos', '2025-08-20 03:42:45'),
(41, 'Física 💫', 'Estudo dos fenômenos da natureza.', '2025-08-20 03:43:43'),
(42, 'Química ⚛️', 'Estudo da matéria, sua composição, estrutura, propriedades e transformações.', '2025-08-20 03:44:57'),
(43, 'Biologia 👩‍🔬', 'Estudo da vida em todas as suas formas.', '2025-08-20 03:45:50'),
(44, 'Ciências Humanas 📚 ', 'Tópicos que estuda Geografia, Filosofia, História e Sociologia juntos.', '2025-08-20 03:47:39'),
(45, 'Literatura 📔 ', 'Estudo da escrita como meio de expressão.', '2025-08-20 03:48:36'),
(46, 'Inglês 🗽 ', 'Estudo da língua inglesa.', '2025-08-20 03:49:18'),
(47, 'Pensamentos e Reflexões do Dia a Dia 🤔', 'Tópico que serve para eu dar opiniões e palpitagens.', '2025-08-20 03:50:17');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `is_admin`, `created_at`) VALUES
(4, 'admin', '$2y$10$J7Pvz6WdjG4Fljj/CjI3TeQHfD9DoZj23C3zGyVJjO/1HcyS1TT1e', 'admin@example.com', 1, '2025-08-13 04:15:09');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_subtopic_id` (`subtopic_id`),
  ADD KEY `idx_data_criacao` (`data_criacao`);

--
-- Índices de tabela `posts_questoes`
--
ALTER TABLE `posts_questoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topico_secundario_id` (`topico_secundario_id`);

--
-- Índices de tabela `questoes`
--
ALTER TABLE `questoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `subtopics`
--
ALTER TABLE `subtopics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_topic_id` (`topic_id`);

--
-- Índices de tabela `topicos_secundarios`
--
ALTER TABLE `topicos_secundarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questao_id` (`questao_id`);

--
-- Índices de tabela `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT de tabela `posts_questoes`
--
ALTER TABLE `posts_questoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `questoes`
--
ALTER TABLE `questoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `subtopics`
--
ALTER TABLE `subtopics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT de tabela `topicos_secundarios`
--
ALTER TABLE `topicos_secundarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT de tabela `topics`
--
ALTER TABLE `topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`subtopic_id`) REFERENCES `subtopics` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `posts_questoes`
--
ALTER TABLE `posts_questoes`
  ADD CONSTRAINT `posts_questoes_ibfk_1` FOREIGN KEY (`topico_secundario_id`) REFERENCES `topicos_secundarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `subtopics`
--
ALTER TABLE `subtopics`
  ADD CONSTRAINT `subtopics_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `topicos_secundarios`
--
ALTER TABLE `topicos_secundarios`
  ADD CONSTRAINT `topicos_secundarios_ibfk_1` FOREIGN KEY (`questao_id`) REFERENCES `questoes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
