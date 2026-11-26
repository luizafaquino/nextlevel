CREATE DATABASE IF NOT EXISTS nextlevel
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE nextlevel;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('cliente', 'admin') DEFAULT 'cliente'
);

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL
);

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT NOT NULL,
    imagem VARCHAR(255) NOT NULL,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    forma_pagamento ENUM('PIX', 'Boleto', 'Cartão') NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

INSERT INTO categorias (nome) VALUES
('Teclados'),
('Mouses'),
('Headsets'),
('Mousepads');

INSERT INTO produtos
(categoria_id, nome, descricao, preco, estoque, imagem)
VALUES

(1, 'Redragon Kumara',
'Teclado mecânico compacto, ideal para jogos e setups com pouco espaço.',
249.90, 10, 'kumara.jpg'),

(1, 'HyperX Alloy Origins',
'Teclado mecânico com estrutura de alumínio e iluminação RGB.',
499.90, 8, 'alloy.jpg'),

(1, 'Logitech G213',
'Teclado gamer com iluminação RGB e apoio para os pulsos.',
299.90, 12, 'g213.jpg'),

(1, 'Razer BlackWidow V3',
'Teclado mecânico gamer com iluminação Razer Chroma RGB.',
599.90, 6, 'blackwidow.jpg'),

(2, 'Logitech G203',
'Mouse gamer com sensor ajustável e iluminação RGB.',
159.90, 15, 'g203.jpg'),

(2, 'Razer DeathAdder V2',
'Mouse gamer com formato ergonômico e sensor óptico.',
249.90, 10, 'deathadder.jpg'),

(2, 'Redragon Cobra',
'Mouse gamer com botões programáveis e iluminação RGB.',
129.90, 12, 'cobra.jpg'),

(2, 'HyperX Pulsefire Haste',
'Mouse gamer leve, desenvolvido para movimentos rápidos e precisos.',
279.90, 8, 'haste.jpg'),

(3, 'HyperX Cloud Stinger 2',
'Headset gamer com almofadas confortáveis e microfone integrado.',
299.90, 10, 'stinger.jpg'),

(3, 'Logitech G435',
'Headset gamer sem fio, leve e com conexão Bluetooth e LIGHTSPEED.',
399.90, 8, 'g435.jpg'),

(3, 'Razer Kraken X',
'Headset gamer leve com microfone flexível.',
259.90, 10, 'kraken.jpg'),

(3, 'Redragon Zeus X',
'Headset gamer com iluminação RGB e microfone integrado.',
329.90, 8, 'zeus.jpg'),

(4, 'Logitech G640',
'Mousepad gamer de tecido com superfície ampla para movimentação.',
129.90, 15, 'g640.jpg'),

(4, 'Razer Gigantus V2',
'Mousepad de tecido com base antiderrapante.',
119.90, 12, 'gigantus.jpg'),

(4, 'HyperX Pulsefire Mat',
'Mousepad gamer com superfície de tecido e bordas costuradas.',
149.90, 10, 'pulsefiremat.jpg'),

(4, 'Redragon Flick',
'Mousepad gamer com superfície de tecido e base emborrachada.',
79.90, 15, 'flick.jpg');

USE nextlevel;



UPDATE produtos SET descricao =
'Teclado mecânico gamer com formato compacto TKL, que elimina o teclado numérico para oferecer mais espaço para o mouse. Possui layout ABNT2, teclas resistentes, atalhos multimídia e acionamento mecânico.'
WHERE id = 1;

UPDATE produtos SET descricao =
'Teclado mecânico equipado com switches HyperX, iluminação RGB individual por tecla e cabo USB-C removível. Possui três ângulos de inclinação, memória para três perfis e personalização pelo software HyperX NGENUITY.'
WHERE id = 2;

UPDATE produtos SET descricao =
'Teclado gamer com teclas Mech-Dome de resposta tátil, iluminação LIGHTSYNC RGB personalizável em cinco zonas e resistência a derramamentos. Conta com apoio integrado para as mãos, inclinação ajustável e controles multimídia dedicados.'
WHERE id = 3;

UPDATE produtos SET descricao =
'Teclado mecânico gamer com estrutura de alumínio, iluminação Razer Chroma RGB e teclas programáveis. Possui controles multimídia, apoio para os pulsos e tecnologia anti-ghosting. Permite personalização pelo software Razer Synapse.'
WHERE id = 4;




UPDATE produtos SET descricao =
'Mouse gamer com fio, sensor com sensibilidade ajustável de 200 a 8.000 DPI e seis botões programáveis. Possui iluminação LIGHTSYNC RGB personalizável e permite ajustar configurações pelo software Logitech G HUB.'
WHERE id = 5;

UPDATE produtos SET descricao =
'Mouse gamer com design ergonômico para destros, sensor óptico Razer Focus+ de até 20.000 DPI e oito botões programáveis. Possui switches ópticos, iluminação Chroma RGB, cabo SpeedFlex e peso aproximado de 82 gramas.'
WHERE id = 6;

UPDATE produtos SET descricao =
'Mouse gamer equipado com sensor Pixart PMW3325 de até 10.000 DPI, sete botões programáveis e iluminação Chroma RGB ajustável. Possui cabo trançado, memória interna para configurações e formato ergonômico para destros.'
WHERE id = 7;

UPDATE produtos SET descricao =
'Mouse gamer ultraleve com aproximadamente 59 gramas sem o cabo, sensor Pixart PAW3335 de até 16.000 DPI e seis botões. Conta com cabo USB HyperFlex, iluminação RGB e base em PTFE para facilitar o deslizamento.'
WHERE id = 8;




UPDATE produtos SET descricao =
'Headset gamer com drivers de 50 mm, almofadas de espuma de memória e revestimento em couro sintético. Possui fones giratórios, controle de volume integrado e microfone com redução de ruído que pode ser silenciado ao levantá-lo.'
WHERE id = 9;

UPDATE produtos SET descricao =
'Headset gamer sem fio com conexão LIGHTSPEED e Bluetooth. Possui estrutura leve e microfones duplos com tecnologia de formação de feixe para reduzir ruídos externos, oferecendo praticidade durante jogos, chamadas e reprodução de músicas.'
WHERE id = 10;

UPDATE produtos SET descricao =
'Headset gamer com estrutura leve, almofadas acolchoadas e microfone flexível. Desenvolvido para proporcionar conforto durante partidas e facilitar a comunicação entre jogadores.'
WHERE id = 11;

UPDATE produtos SET descricao =
'Headset gamer com drivers de 53 mm, áudio surround virtual 7.1 e iluminação Chroma RGB com quatro efeitos. Possui almofadas de tecido, microfone com redução de ruídos, conexão USB e controles de áudio integrados ao cabo.'
WHERE id = 12;




UPDATE produtos SET descricao =
'Mousepad gamer de tecido com dimensões de 460 por 400 mm. Sua superfície apresenta atrito moderado para facilitar o controle do mouse, enquanto a base emborrachada ajuda a manter o produto firme sobre a mesa.'
WHERE id = 13;

UPDATE produtos SET descricao =
'Mousepad gamer com superfície de tecido de microtrama, desenvolvido para oferecer equilíbrio entre velocidade e controle. Possui espuma de borracha de alta densidade e base antiderrapante. Disponível em diferentes tamanhos.'
WHERE id = 14;

UPDATE produtos SET descricao =
'Mousepad gamer produzido com tecido de trama densa, superfície uniforme e bordas costuradas para evitar o desgaste. Possui base de borracha antiderrapante e estrutura flexível que facilita o transporte.'
WHERE id = 15;

UPDATE produtos SET descricao =
'Mousepad gamer desenvolvido para proporcionar movimentos rápidos e uniformes do mouse. Sua linha conta com quatro opções de tamanho, adequadas a diferentes espaços de mesa e estilos de jogo.'
WHERE id = 16;