'use strict';

let testAry = [1,2,3,4];
let tempAry = [...testAry];
console.log( 'tempAry.reverse:::',tempAry.reverse() );
console.log( 'testAry:::',testAry );
console.log( 'tempAry:::',tempAry );

// イメージのフォルダ
const imageFOLDER = './img/';
// カードの配列
const kashiwaCard_garas = ['spade', 'heart', 'club', 'diamond']; // 柄
const kashiwaCard_nums = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13']; // 数字

// [4] プリロード機能の実装
for( let gara of kashiwaCard_garas) {
	//console.log( gara );
	for( let num of kashiwaCard_nums) {
		const PATH = `${imageFOLDER}card_${gara}_${num}.png`;
		//console.log( PATH );
		preloadImage( PATH );
	}
}
//
function preloadImage( path ) {
	let imgTag = document.createElement('img');
	imgTag.src = path;
}
//
// [5] 時間情報の取得と利用
const goaisatsuElement = document.getElementById('goaisatsu');
if( goaisatsuElement ) {
	function GetNowMessage() {
		const now = new Date();
		//
		const nowYear = now.getFullYear();
		const nowMonth = now.getMonth() + 1;
		const nowDate = now.getDate();
		const nowDay = now.getDay();
		//
		const nowHour = now.getHours();
		const nowMin = now.getMinutes();
		const nowSec = now.getSeconds();
		//
		let myMessage = '';
		if( 6 <= nowHour && nowHour <= 10 ) {
			myMessage = 'おはようございます。<br>';
		}
		else if( 12 <= nowHour && nowHour <= 14 ) {
			myMessage = 'こんにちは。<br>';
		}
		else if( 19 <= nowHour && nowHour <= 21 ) {
			myMessage = 'こんばんは。<br>';
		}
		//
		const nowWeekDays = ['日', '月', '火', '水', '木', '金', '土'];
		myMessage += `令和 ${nowYear-2018} 年　${nowMonth} 月　${nowDate} 日　${nowWeekDays[nowDay]} 曜日<br>`;
		myMessage += `${( nowHour < 12 ) ? '午前' : '午後'} ${nowHour % 12} 時　${nowMin} 分　${nowSec} 秒　に起動しました。<br>`;
		//
		return myMessage;
	}
	//console.log( GetNowMessage() );
	goaisatsuElement.innerHTML=GetNowMessage();
}
//
// [2] 配列の制作・使用
// 差し替え用のカードを取得
const myCardBoxElement = document.querySelector( '#imagePage_card' );
const myCardImgElement = document.querySelector( '#imagePage_card img[alt="トランプの表面"]' );
if ( myCardBoxElement ){
	// myCardBoxElementがあれば...
	let cardOpenIs = false;
	myCardBoxElement.addEventListener( 'mousedown', ClickCard_imagePage );
	//
	function ClickCard_imagePage( ev ) {
		//console.log(ev);
		/*while( myCardBoxElement.children.length > 0 ){
			myCardBoxElement.removeChild( myCardBoxElement.children[0] );
		}*/
		let selectCard = 'card_'
		// if( cardOpenIs ){
		// 	// カードが表の時
		// 	// カードを裏返す
		// 	selectCard += 'back';
		// 	cardOpenIs = false;
		// }
		// else {
		// 	// カードが裏の時
		// 	// 表カードの選択
		// 	selectCard += `${kashiwaCard_garas[Math.floor(Math.random()*4)]}_${kashiwaCard_nums[Math.floor(Math.random()*13)]}`;
		// 	cardOpenIs = true;
		// }
		selectCard += `${kashiwaCard_garas[Math.floor(Math.random()*4)]}_${kashiwaCard_nums[Math.floor(Math.random()*13)]}`;
		// 表示カードの更新
		//myCardBoxElement.innerHTML = `<img src="${imageFOLDER}${selectCard}.png">`;
		myCardImgElement.src = `${imageFOLDER}${selectCard}.png`;

	}
}
//
// [3] 繰り返し処理
const myTableElement = document.getElementById('veiwTrThTd');
//
if( myTableElement ) {
	// myTableElementがあれば...
	for(let i = 1; i <= 9; i++ ) {
		let addline = '';
		for(let j=0; j <= 9; j++ ){
			if( j === 0 ) {
				addline = `<tr><th>${i}</th>`;
			}
			else {
				addline += `<td>${i * j}</td>`;
			}
		}
		addline += '</tr>';
		//
		myTableElement.insertAdjacentHTML( 'beforeend', addline );
	}
}
//
// [7] オブジェクトの作成・使用
if( document.getElementById( 'putCardsBG' ) ) { // カードの表示枠があれば
	// 手札クラス
	class MyCardsC {
		constructor( maisu=-1, garaMAX=-1, numMAX=-1 ) {
			//this.cards = cards // 手札の枚数で初期化する様に初期化方法を変更
			this.cards = [];
			this.length = maisu; // 手札にする枚数
			this.cardNumMAX = numMAX; // カードの数の種類
			this.cardGaraMAX = garaMAX; // カードの柄の種類
			
			this.tempMap = [];
			this.cardNumMap = [];
			this.cardGaraMap = [];
			
			this.flushIs = false;
			this.royalIs = false;
			this.straightIs = false;
			this.twoNum = 0;
			this.threeNum = 0;
			this.fourNum = 0;

			// カード選択可能か否か
			this.selectable = false;
			//
			// ゲームの情報
			this.gameInfo={
				yamaLength : 0,
			};

			// 手札情報(役に関するもの)
			this.info = {
				flushIs : this.flushIs,
				royalIs : this.royalIs,
				straightIs : this.straightIs,
				twoNum : this.twoNum,
				threeNum : this.threeNum,
				fourNum : this.fourNum
			}; 
			// 手札情報(表示に関するもの)
			this.mySelect = {
				list : [],
				initialize : function(num) {
					this.list=[];
					for(let i=0; i<num; i++) {
						this.list.push(false);
					}
				},
				trueCount : function() {
					let count = 0;
					//console.log('true check ',this.list);
					for(let i=0; i<this.list.length; i++) {
						if( this.list[i] ) {
							count++;
						}
					}
					return count;
				}
			};
			this.mySelect.initialize( this.length ); // 一旦初期化して使えるようにしておく
			console.log('initialized::',this.mySelect.list);
			//
			//手札の表示関係
			this.garaNames = kashiwaCard_garas; // 柄のリスト
			this.numNames = kashiwaCard_nums; // 数のリスト
			//
			this.imgEventListeners = []; // 設定するイベントリスナーの管理用
			// カードのクリック対応で...一旦
			this.selectToggle = ( event ) => {
				if( !this.selectable ) { return; } // 選択可能になっていなければ、何もしない
				//console.log('click:::', event.target);
				// クリック要素(画像)のindexを取得
				let index = 0;
				for(let i=0; i<this.length; i++) {
					if( event.target === this.putCardsElement.children[i] ) {
						index = i;
						break;
					}
				}
				//console.log('index is ',index);
				//
				// mySelectが初期化されていない場合は、初期化する。
				if(this.mySelect.list.length === 0) { this.mySelect.initialize(this.length); }
				//
				if( !this.mySelect.list[index] ) {
					// やまの残りのカード枚数との比較
					//console.log('gameInfo(test)',this.gameInfo);
					//console.log('gameInfo::', this.yamaLength);
					//console.log('trueCount::', this.mySelect.trueCount());
					if( this.mySelect.trueCount()+1 > this.gameInfo.yamaLength ){
						// 追加すると交換希望枚数がカードの残り枚数をこえてしまう時...
						alert('カードの残り枚数が足りません');
						return;
					}
					else {
						// mySelectへの登録
						this.mySelect.list[index] = true;
					}
				}
				else {
					this.mySelect.list[index] = false;
				}
				//console.log(this.mySelect.list);
				// 枠の表示を更新
				this.viewFrame( index, this.mySelect.list[index] );
			}
		}
		//
		initialize( ) {
			if ( this.cardNumMAX <= -1 || this.cardGaraMAX <= -1 ) {
				// カードの種別の設定が未完成の時 エラーをログに排出
				console.log('カード種別の初期化に不備がありました:::');
				console.log(`cardNumMAX  :: ${this.cardNumMAX}`);
				console.log(`cardGaraMAX :: ${this.cardGaraMAX}`);
				console.log('-----------------------------');
				//
				return false;
			}
			// 初期化 カードの表示場所の確認
			this.putCardsElement = document.getElementById( 'putCardsBG' ); // カードの表示枠
			if( this.putCardsElement ) {
				// カード表示枠があれば...(一旦カードの表示場所も確保されているとしておく)
				// 子要素にクリック設定
				if( this.putCardsElement.children.length === this.length ) {
					// 一旦 子要素はすべてカード表示用の<img>だとしておく
					for(let i=0; i<this.length; i++ ) {
						// リスナーを設定
						this.imgEventListeners[i] = this.putCardsElement.children[i].addEventListener('click', this.selectToggle );
					}
				}
				else{
					alert('カードの表示枠と手札の数が合いません。ご確認を');
				}
			}
		}
		newCards( cards ){
			// 新規カード
			this.cards = cards
			this.length = cards.length;
			//
			// 初期化
			this.tempMap = [];
			this.cardNumMap = [];
			this.cardGaraMap = [];
			
			this.flushIs = false;
			this.straightIs = false;
			this.twoNum = 0;
			this.threeNum = 0;
			this.fourNum = 0;
			//
			this.mySelect.initialize(); // 交換希望リストの初期化
		}
		//
		static initCardMap( MAX ) {
			// Mapの初期化(0で埋める)
			const list =[];
			for(let i=0; i<MAX; i++) {
				list.push(0);
			}
			return list;
		}
		
		buildCardMap( categ ) {
			// 各mapの初期化
			let MAX = 0;
			if( categ === 'num' ) {
				MAX = this.cardNumMAX;
			}
			else if( categ === 'gara' ) {
				MAX = this.cardGaraMAX;
			}
			else {
				return false;
			}
			this.tempMap =  MyCardsC.initCardMap( MAX );
			//
			// Map生成
			for(let i=0; i<this.cards.length; i++) {
				this.tempMap[ this.cards[i][categ] ]++;
			}
			// 生成Mapの格納
			if( categ === 'num' ) {
				this.cardNumMap = this.tempMap;
			}
			else if( categ === 'gara' ) {
				this.cardGaraMap = this.tempMap;
			}
			//
			//console.log('cardNumMap:::',this.cardNumMap);
			//console.log('cardGaraMap::',this.cardGaraMap);
			return true;
		}
		
		// flushの確認
		static checkFlush( map, length ) {
			// カードすべてが同じ柄
			//console.log('map::', map);
			//console.log('length::', length);
			return ( map.indexOf( length ) != -1 ) ? true : false;
		}
		// 数の確認
		static checkNum( map, length ) {
			// 各値の初期化
			let straightIs = false;
			let royalIs = false;
			let twoNum = 0;
			let threeNum = 0;
			let fourNum = 0;
			//
			let firstNum = -1; // 初出待避用
			let sum = 0; // カード合算用
			//
			for(let i=0; i<map.length; i++) {
				if( map[i] === 1 ) { // ストレートの可能性
					if( firstNum === -1 ) { // 初めて1枚登録の数字が見つかったら...
						firstNum = i; // 後処理のために待避しておく
					}
					sum += i; // 1枚のカードを全て足していく
				}
				else if( map[i] == 2 ) {
					// 同じ数字が2枚
					twoNum++; // カウント
					continue; // フルハウス,ツーペアの可能性を考慮して次のターンへ
				}
				else if( map[i] == 3 ) {
					// 同じ数字が3枚
					threeNum++; // カウント
					continue; // フルハウスの可能性を考慮して次のターンへ
				}
				else if( map[i] == 4 ) {
					// 同じ数字が3枚
					fourNum++; // カウント
					break; // ポーカーでは4枚+αの役がないのでループを出る
				}
			}
			// 2枚組,3枚組,4枚組がないとき...
			if( twoNum + threeNum + fourNum == 0 ) {
				//console.log('sum::',sum,',sa::',sum-(firstNum*5));
				if( sum - ( firstNum * 5 ) === 10 ) { // ストレート チェック
					//console.log('check');
					straightIs = true;
				}
				else if( sum - ( firstNum * 5 ) === 9 + 10 + 11 + 12 ) { // ロイヤルストレート チェック
					straightIs = true;
					royalIs = true;
				}
			}
			/*
			if( twoNum + threeNum + fourNum == 0 ) {
				let doDel = false;
				// 連続している桁数を調べたい
				// 後ろから調べる
				for(let i = map.length-1; i >= 0; i=map.length-1) {
					if( map[i] !== 0 ) {
						// 0以外に当たれば、チェックループを抜ける
						break;
					}
					else {
						// 0ならお尻を抜く
						map.pop();
						doDel = true; // 削除実行フラグを付ける
					}
				}
				// お尻を抜いていない時、Aが循環している可能性がある
				if( !doDel && map[0] !== 0 ) {
					//頭が0じゃなければ、頭を抜いてお尻に付ける
					map.push(map[0]);
					map.shift();
				}
				else {
					doDel = true; // K始まりのストレート対策で仮
				}
				// 前から調べる
				for(let i = 0; i < map.length; i=0 ) {
					if( map[i] !== 0 ) {
						// 0以外に当たれば、チェックループを抜ける
						break;
					}
					else {
						// 0なら頭を抜く
						map.shift();
					}
				}
				// 頭とお尻が0始まりでなくなったので...
				console.log('mapCheck:::',map);
				if( map.length === length ) {
					// 手札全部で数字がつながっていれば
					straightIs = true;
					if( !doDel && map[0] !== 0 ) {
						royalIs = true;
					}
				}
			}
			*/
			//
			return {
				straightIs: straightIs,
				royalIs: royalIs,
				twoNum: twoNum,
				threeNum: threeNum,
				fourNum: fourNum
			};
		}

		checkMyCards() {
			// 役の確認
			this.flushIs = MyCardsC.checkFlush( this.cardGaraMap, this.length );
			let myObj = MyCardsC.checkNum( this.cardNumMap, this.length );
			//console.log(myObj);
			// 各値の更新
			this.straightIs = myObj.straightIs;
			this.royalIs = myObj.royalIs;
			this.twoNum = myObj.twoNum;
			this.threeNum = myObj.threeNum;
			this.fourNum = myObj.fourNum;
			//
			myObj.flushIs = this.flushIs
			//
			this.info = myObj;
			//
			return myObj;
		}

		cardSort() {
			// 向かって左から昇順(1~13)にソート
			for(let i = 0; i < this.length; i++) {
				let currentNo = this.length - 1; // 検査手札
				for(let targetNo = currentNo - 1; targetNo >= i; targetNo--) { // 比較手札
					// 札の数字を比較
					if( (this.cards[ currentNo ].num < this.cards[ targetNo ].num) ||
						((this.cards[currentNo ].num === this.cards[ targetNo ].num) &&
							this.cards[ currentNo ].gara < this.cards[ targetNo ].gara) ) {
						// 検査手札が比較手札より小さい時 => 入れ替える
						// 検査手札と比較手札と数字が同じだった時は、柄のindexが小さい時 => 入れ替える
						const tempCard = this.cards[ currentNo ];
						this.cards[ currentNo ] = this.cards[ targetNo ];
						this.cards[ targetNo ] = tempCard;
					}
					currentNo = targetNo; // 小さい方を検査手札に更新
				}
			}
			return this.cards;
		}
		// カード枠
		viewFrame( index, flg ) {
			// indexとflgをもらって、indexのカード枠の表示をON/OFF
			const targetElement = this.putCardsElement.children[ index ];
			if( flg ) {
				targetElement.style.borderWidth='2px';
				targetElement.style.borderStyle='solid';
				targetElement.style.borderColor='yellow';
			}
			else {
				targetElement.style.borderWidth='2px';
				targetElement.style.borderStyle='solid';
				targetElement.style.borderColor='transparent';
			}
		}
		// カードの表示
		viewCards() {
			for(let i = 0; i < this.putCardsElement.children.length; i++) {
				const element = this.putCardsElement.children[i];
				const selectCard = 'card_' + this.garaNames[this.cards[i].gara] + '_' + this.numNames[this.cards[i].num];
				//console.log(selectCard);
				//console.log(this.cards[i].name);
				element.src = `${imageFOLDER}${selectCard}.png`;
				element.alt = `${this.cards[i].name}`;
			}
		}
	}
	/** 手札クラスのテスト用 **
	const testCards = [
		{gara:0,num:12},
		{gara:0,num:9},
		{gara:0,num:5},
		{gara:0,num:11},
		{gara:0,num:0}
	];
	const testest = new MyCardsC(testCards);
	console.log(testest);
	console.log('cards',testest.cards);
	console.log('length',testest.length);
	console.log('buildNumMap', testest.buildCardMap( 'num' ));
	console.log('buildGaraMap', testest.buildCardMap( 'gara' ));
	console.log('numMap', testest.cardNumMap);
	console.log('garaMap', testest.cardGaraMap);
	let myCardsInfo = testest.checkMyCards();
	console.log('flushIs',testest.flushIs);
	console.log('straightIs',testest.straightIs);
	console.log('twoNum',testest.twoNum);
	console.log('threeNum',testest.threeNum);
	console.log('fourNum',testest.fourNum);
	console.log('cardSort:::',testest.cardSort());
	testest.viewCards();
	/**/////////////////////////////////////// 手札クラス

	// カード山クラス
	class CardYamaC {
		constructor( garas, nums){
			this.garas = garas;
			this.nums = nums;
			this.yama = []; // ヤマ
			
			// 表示関係
			this.cardRirekiViewElement = document.querySelector( 'div.cardRirekiView' ); // 使用カードの履歴
			this.viewRestCardElement = document.getElementById( 'viewRestCard' ); // ヤマののこり枚数
		}
		static nameGet(gara,num) {
			// カードの名前に付ける
			let name = '';
			switch( kashiwaCard_garas[ gara ] ) {
				case 'spade' : name = 'スペードの'; break;
				case 'heart' : name = 'ハートの'; break;
				case 'club' : name = 'クラブの'; break;
				case 'diamond' : name = 'ダイアの'; break;
			}
			if( num+1 === 1 ) { name += 'A'; }
			else if( num+1 === 11 ) { name += 'J'; }
			else if( num+1 === 12 ) { name += 'Q'; }
			else if( num+1 === 13 ) { name += 'K'; }
			else { name += num+1; }
			//
			return name;
		}
		initialize() {
			// ヤマの新造
			this.newYama();
			//
			// 使用カード履歴の表示部分をタイトルのみにする
			while( this.cardRirekiViewElement.children.length > 1 ) {
				this.cardRirekiViewElement.removeChild( this.cardRirekiViewElement.children[this.cardRirekiViewElement.children.length-1] );
			}
			// 使用カード履歴の初期化
			for(let i=0; i<this.garas.length; i++) {
				let addline = `<div class="rirekiLine${i}">`;
				addline += `<span class="rirekiHead">${this.garas[i]}：</span>`;
				for(let j=0; j<this.nums.length; j++) {
					addline += `<span class="cardNum${j}">${this.nums[j]} </span>`;
				}
				addline += '</div>';
				//
				this.cardRirekiViewElement.insertAdjacentHTML( 'beforeend', addline);
			}
			// つぎ来るカード
			if( !document.getElementById('nextCardView') ) { // idが'nextCardview'の要素がなければ...
				let nextCardInfoView = `<div id="nextCardView"><span>Next：<span></div>`;
				this.cardRirekiViewElement.insertAdjacentHTML( 'afterbegin', nextCardInfoView);
				const nextViewElement = document.getElementById('nextCardView');
				nextViewElement.style.backgroundColor = '#ddd';
				nextViewElement.style.borderRadius = '5px';
				nextViewElement.style.padding = '2px 5px';
				nextViewElement.style.marginBottom = '5px';
			}
		}
		// ヤマつくり
		newYama() {
			this.yama = []; // ヤマ初期化
			for(let num = 0; num < this.nums.length; num++) {
				for(let gara = 0; gara < this.garas.length; gara++) {
					this.yama[num * 4 + gara] = {
							gara: gara,
							num: num,
							card: [gara, num],
							name: CardYamaC.nameGet(gara,num),
					};
				}
			}
		}
		// シャッフル
		shuffle( MAX ) {
			for(let s = 0; s < MAX; s++){
				let index1 = Math.floor( Math.random() * this.yama.length );
				let index2 = Math.floor( Math.random() * this.yama.length );
				let temp = this.yama[ index1 ];
				this.yama[ index1 ] = this.yama[ index2 ];
				this.yama[ index2 ] = temp;
			}
			return this.yama;
		}
		// カードを配る
		deal( num ) {
			const dealCards = [];
			for(let i=0; i<num; i++) {
				dealCards.push( this.yama.pop() );
				CardYamaC.viewCardRireki( dealCards[i] ); // 使用カード履歴の表示
			}
			// 配ったので、使用カードの表示を更新
			this.viewRestCardElement.textContent = this.yama.length;
			//
			return dealCards;
			//
		}
		// 使用カードの履歴を更新
		static viewCardRireki( card ){
			//console.log('cardRireki::',card);
			const testElement = document.querySelector( `div.rirekiLine${card.gara} span.rirekiHead~span.cardNum${card.num}` );
			testElement.style.color = '#aaa';
			//
		}
		// つぎ来るカードを更新
		viewNextCard( debugFlg ) {
			//つぎ来るカード更新
			const nextViewElement = document.getElementById('nextCardView');
			if ( !debugFlg ) { // デバグがOFFの時は非表示に
				nextViewElement.style.display = 'none';
				return; // 非表示なので、処理しないで抜ける
			}
			else {
				nextViewElement.style.display = 'block';
			}
			//
			while(nextViewElement.children.length > 1) {
				nextViewElement.removeChild(nextViewElement.children[nextViewElement.children.length-1]);
			}
			let addline = '';
			//console.log('this.yama:::',this.yama);
			//console.log('this.yama.length:::',this.yama.length);
			//for(let i=this.yama.length-1; i>=0; i--){ // 全部表示
			for(let i=this.yama.length-1; (i>=0 && i>(this.yama.length-1)-5); i--){ // 5枚分表示
				addline += `<span>[${this.yama[i].name}]</span>`;
			}
			if( this.yama.length > 5 ) { // 5枚表示の時に、残りが５枚以上あることを伝える
				addline += `<span> => </span>`;
			}
			nextViewElement.insertAdjacentHTML( 'beforeend', addline);
			//nextViewElement.style.height = '20px';
			// nextViewElement.style.overflow = 'hidden'; // テキスト溢れを示す処理 (溢れを表示しない)
			// nextViewElement.style.whiteSpace = 'nowrap'; // テキスト溢れを示す処理 (折り返しなし)
			// nextViewElement.style.textOverflow = 'ellipsis'; // テキスト溢れを示す処理 (溢れを...で示す)
		}
		//
	}
	/** カード山クラス テスト用 **
	const testYama = new CardYamaC( kashiwaCard_garas, kashiwaCard_nums );
	testYama.initialize();
	console.log('testYama::',testYama.yama);
	console.log('testShuffle::',testYama.shuffle( 200 ));
	const testCards = testYama.deal(5);
	console.log('dealCards', testCards);
	//
	const testest = new MyCardsC(testCards);
	console.log('buildNumMap', testest.buildCardMap( 'num' ));
	console.log('buildGaraMap', testest.buildCardMap( 'gara' ));
	console.log('numMap', testest.cardNumMap);
	console.log('garaMap', testest.cardGaraMap);
	let myCardsInfo = testest.checkMyCards();
	testest.cardSort();
	testest.viewCards();
	/**/

	// ポーカーゲームクラス
	class GamePorkerC {
		constructor() {
			this.onGameIs = false; // ゲーム中 true/false
			this.gameCount = 0; // ゲームカウント
			this.changeMAX = 3; // 1ゲームでの交換可能回数
			this.changeCount = 0; // 交換回数の残り
			this.nowYakuNo = 0; // 現状の役番号
			this.yakuResults = []; // 役の履歴
			// 役関係
			this.yakus = [
				{
					No : 0, name : 'ロイヤルストレートフラッシュ',
					setsumei : '手札を同じマークで10・J・Q・K・Aと揃えると、<br><span>「ロイヤルストレートフラッシュ」</span>という役になります。'
				}, {
					No : 1, name : 'ストレートフラッシュ',
					setsumei : '手札の5枚が、同じマークで数字が連続していると、<br><span>「ストレートフラッシュ」</span>という役になります。'
				}, {
					No : 2, name : 'フォーカード',
					setsumei : '手札の5枚で、同じ数字を4枚揃えると、<br><span>「フォーカード」</span>という役になります。'
				}, {
					No : 3, name : 'フルハウス',
					setsumei : 'スリーカードとワンペアの組み合わせで、<br><span>「フルハウス」</span>という役になります。'
				}, {
					No : 4, name : 'フラッシュ',
					setsumei : '手札の5枚すべてを同じ柄で揃えると、<br><span>「フラッシュ」</span>という役になります。'
				}, {
					No : 5, name : 'ストレート',
					setsumei : '手札の5枚が、柄に関係なく数字を連続して揃えると、<br><span>「ストレート」</span>という役になります。'
				}, {
					No : 6, name : 'スリーカード',
					setsumei : '手札の5枚のうち、同じ数字を3枚揃えると、<br><span>「スリーカード」</span>という役になります。'
				}, {
					No : 7, name : 'ツーペア',
					setsumei : '手札の5枚のうち、同じ数字2枚の組が2組あれば、<br><span>「ツーペア」</span>という役になります。'
				}, {
					No : 8, name : 'ワンペア',
					setsumei : '手札の5枚のうち、同じ数字2枚の組が1組あれば、<br><span>「ワンペア」</span>という役になります。'
				}, {
					No : 9, name : 'ぶた',
					setsumei : '何の役もできていなければ、ただの<span>「ブタ」</span>だ。'
				}
			];
			// 表示類
			this.gameCountElement = document.getElementById( 'viewTurn' ); // ゲームカウント表示場所
			this.changeCountElement = document.getElementById( 'viewRestChange' ); // 交換回数の表示場所
			this.titleYakuNameElement = document.getElementById( 'titleYakuName' ); // タイトル(役名)表示場所
			this.putCardsInfoElement = document.getElementById( 'putCardsInfo' ); // 説明の表示場所
		}

		initialize() {
			// 役の履歴の初期化
			this.yakuResults = [];
			for(let i=0; i < this.yakus.length; i++) {
					this.yakuResults.push(0);
			}
		}
		newTurn( ) {
			// 新規ゲームの準備
			this.gameCount++; //ゲームカウントの更新
			this.changeCount = this.changeMAX;
			// 表示の更新
			this.gameCountElement.textContent = this.gameCount; // 何試合目か
			this.changeCountElement.textContent = this.changeCount; // 交換回数
			this.putCardsInfoElement.innerHTML = ''; // 説明の表示
		}
		judge( cardStatus ) {
			console.log(cardStatus);
			// 役の判定
			let yakuName = 'ぶた';
			if( cardStatus.straightIs ) { 
				if( cardStatus.royalIs && cardStatus.flushIs ) { yakuName = 'ロイヤルストレート'; }
				else { yakuName = 'ストレート'; }
			}
			else if( cardStatus.fourNum === 1) { yakuName = 'フォーカード'; }
			else if( cardStatus.threeNum === 1 ) {
				if( cardStatus.twoNum === 1) { yakuName = 'フルハウス'; }
				else { yakuName = 'スリーカード'; }
			}
			else if( cardStatus.twoNum === 2 ) { yakuName = 'ツーペア'; }
			else if( cardStatus.twoNum === 1 ) { yakuName = 'ワンペア'; }
			if( cardStatus.flushIs ){
				if( cardStatus.straightIs ) {
					yakuName += 'フラッシュ';
				}
				else {
					yakuName = 'フラッシュ';
				}
			}
			console.log( 'yakuName',yakuName );
			// 役情報の取得
			for(let i=0; i<this.yakus.length; i++) {
				if( yakuName === this.yakus[i].name ) {
					this.nowYakuNo = i; // 現状の役番号を退避
					this.titleYakuNameElement.textContent = this.yakus[i].name; // 役名更新
					return this.yakus[i]; // 結果の役情報を返す
				}
			}
		}
		// 役の確定
		fix() {
			console.log('fixCheck');
			// 役名に[確定]処理
			//this.titleYakuNameElement.textContent += '【確定】';
			this.titleYakuNameElement.insertAdjacentHTML( 'beforeend', '<span class="kakutei">【確定】</span>' );
			//this.titleYakuNameElement.querySelector('span').style.color = '#f33'; // cssでの指定に変更
			//this.titleYakuNameElement.querySelector('span').style.display = 'inline-block';
			//this.titleYakuNameElement.querySelector('span').style.animationName = 'stampAnime';
			//this.titleYakuNameElement.querySelector('span').style.animationDuration = '0.15s';
			//this.titleYakuNameElement.querySelector('span').style.animationTimingFunction = 'ease';
			//this.titleYakuNameElement.querySelector('span').style.animationFillMode = 'forwards';
			this.yakuResults[this.nowYakuNo]++; // 結果のカウント
			// 説明の表示
			this.putCardsInfoElement.innerHTML = `ポーカーでは、<br class="onlySP">${this.yakus[this.nowYakuNo].setsumei}`;
		}
	}
	/**/
	/*ゲームクラステスト
	const gameTest = new GamePorkerC();
	gameTest.newGame();
	gameTest.judge(myCardsInfo);
	/**/

	// ゲーム生成
	const INIT = 0;
	const NEW_GAME = 1;
	const TURN_START = 2;
	const CARD_CHANGE = 3;
	const UPDATA_INFO = 4;
	const UPDATA_VIEW = 5;
	const WAIT = 6;
	const CARD_FIX = 7;
	const TURN_END = 8;
	const GAME_END = 9;

	const myTefuda = 5;
	const shuffleNUM = 200; // シャッフル回数

	let debugModeIs = false; // デバグモード フラグ

	// 各ボタン取得
	const judgeBtnElement = document.getElementById( 'judgeBTN' ); // [確定]ボタン
	const nextBTNElement = document.getElementById( 'nextBTN' ); // [次へ]ボタン
	const sortBtnElement = document.getElementById( 'sortBTN' ); // [ソート]ボタン
	const koukanBtnElement = document.getElementById( 'koukanBTN' ); // [交換]ボタン
	const finishBtnElement = document.getElementById( 'finishBTN' ); // [終了]ボタン
	const debugBtnElement = document.getElementById( 'debugBTN' ); // [デバグ]ボタン

	// 初期化
	const testCards = [
		{gara:0,num:12},
		{gara:0,num:9},
		{gara:0,num:5},
		{gara:0,num:11},
		{gara:0,num:0}
	]; // テスト用 カード
	//
	// クラスをインスタンス化
	const myCards = new MyCardsC( myTefuda, kashiwaCard_garas.length, kashiwaCard_nums.length ); // 手札
	const myTrumps = new CardYamaC( kashiwaCard_garas, kashiwaCard_nums ); // トランプセット
	const myGame = new GamePorkerC(); // ゲーム管理
	// 
	//ゲーム進行
	function gameView( gameNo ) {
		switch(gameNo) {
			case INIT :
				// 初期化
				myCards.initialize();
				myTrumps.initialize();
				myGame.initialize();
				// 一旦すべてのボタンを非表示に
				judgeBtnElement.style.visibility = 'hidden';
				nextBTNElement.style.visibility = 'hidden';
				koukanBtnElement.style.visibility = 'hidden';
				finishBtnElement.style.visibility = 'hidden';
				//
			case NEW_GAME :
				// ゲーム開始として...
				myGame.onGameIs = true;
				// 新規ゲーム処理
				myTrumps.shuffle( shuffleNUM ); // トランプのシャッフル
			case TURN_START :
				// ターン開始
				myGame.newTurn( debugModeIs ); //新規ターン
				myCards.newCards( myTrumps.deal( 5 ) ); // トランプを配って手札にする
				// 手札のソート
				myCards.cardSort(); //手札をソート
			case CARD_CHANGE:
				// カード交換
				for(let i=0; i<myCards.mySelect.list.length; i++){
					if( myCards.mySelect.list[i] ) { // 交換希望のあるカードを...
						// ヤマから引いて1枚ずつ交換
						myCards.cards[i] = myTrumps.deal(1)[0];
					}
				}
				// 選択状態の解除
				myCards.mySelect.initialize();
				for(let i=0; i<myCards.length; i++) {
					myCards.viewFrame( i, myCards.mySelect.list[i] );
				}
			// 交換可能回数の減少・表示更新
				if( !debugModeIs ){
					myGame.changeCount--;
					//myGame.changeCountElement.textContent = myGame.changeCount;
				}
			case UPDATA_INFO :
				// ヤマ情報を手札に渡す
				myCards.gameInfo.yamaLength =  myTrumps.yama.length;
				// 手札情報の更新
				myCards.buildCardMap( 'gara' ); // 柄mapの作成
				myCards.buildCardMap( 'num' ); // 数mapの作成
				myCards.checkMyCards(); // 手札情報を更新
				// ポーカー役の確認
				myGame.judge( myCards.info );
			case UPDATA_VIEW :
				//手札の表示
				myCards.viewCards(); // 手札を表示
			case WAIT :
				// 操作待ち
				myTrumps.viewNextCard( debugModeIs ); // 次来るカード表示の更新(デバグ用)
				if( debugModeIs ) {
					myGame.changeCountElement.textContent = '∞'
				}
				else {
					myGame.changeCountElement.textContent = myGame.changeCount;
				}
				if( myGame.changeCount <= 0 ) {
					// 交換可能回数が0になったら...
					myCards.selectable = false; // カードの選択を不可に
					//gameView( CARD_FIX ); // カードFIX処理へ
					judgeBtnElement.dispatchEvent(new Event('click'));
					return; // 念の為 関数を抜けておく
				}
				else {
					myCards.selectable = true; // カードを選択可能に
				}
				// ボタン表示の更新
				judgeBtnElement.style.visibility = 'visible';
				nextBTNElement.style.visibility = 'hidden';
				koukanBtnElement.style.visibility = 'visible';
				finishBtnElement.style.visibility = 'visible';
				break;
			case CARD_FIX :
				// カードを確定
				myCards.selectable = false; // カードを選択不可に
				// 選択状態の解除
				myCards.mySelect.initialize();
				for(let i=0; i<myCards.length; i++) {
					myCards.viewFrame( i, myCards.mySelect.list[i] );
				}
				myGame.fix(); // 現在の手札で役確定
			case TURN_END :
				// ターン終了(次回の配布待ち)
				// ボタン表示の更新
				judgeBtnElement.style.visibility = 'hidden';
				nextBTNElement.style.visibility = 'visible';
				koukanBtnElement.style.visibility = 'hidden';
				finishBtnElement.style.visibility = 'visible';
				//
				// ヤマの残り枚数から次のターンが行えるかを判定
				if( myTrumps.yama.length < myCards.length ) {
					// ヤマの残りが手札の枚数に届かない時...
					nextBTNElement.style.visibility = 'hidden';
				}
				break;
			case GAME_END :
				// ゲーム終了
				myGame.onGameIs = false; // ゲーム終了としてフラグ設定
				// 終了告知
				myGame.titleYakuNameElement.textContent = 'ゲーム終了!!';
				// 次へボタンの表記の変更
				nextBTNElement.textContent = '次のゲームへ';
				// ボタン表示の更新
				judgeBtnElement.style.visibility = 'hidden';
				nextBTNElement.style.visibility = 'visible';
				koukanBtnElement.style.visibility = 'hidden';
				finishBtnElement.style.visibility = 'hidden';
				break;
		};
	}
	gameView( INIT );//後で位置を変える

	// 確定ボタンの処理
	judgeBtnElement.addEventListener( 'click', judgeBtnFunc );
	//
	function judgeBtnFunc( ev ) {
		//console.log('judgeBtnFunc',myGame.changeCount);
		// カードのFIX処理へ
		gameView( CARD_FIX );
	}
	//
	// 次へボタンのへのリスナー設定
	nextBTNElement.addEventListener( 'click', nextBTNFunc);
	function nextBTNFunc( ev ) {
		if( myGame.onGameIs ) { // ゲーム中
			// 配るボタンの対応
			if( myTrumps.yama.length < myCards.length ){
				// フルで配ることができない
				return; // 何もしない
			}
			else{
				// 新しいターンの開始へ
				gameView( TURN_START );
			}
		}
		else {
			// [次のゲームへ]状態でのクリックなので、表記を戻す
			ev.target.textContent = 'つぎへ';
			gameView( INIT ); // NEW_GAMEへ
		}
	}
	//
	// ソートボタン対応
	sortBtnElement.addEventListener( 'click', sortBtnFunc );
	function sortBtnFunc( ev ) {
		// 手札の選択を解除
		myCards.mySelect.initialize();
		for(let i=0; i<myCards.length; i++) {
			myCards.viewFrame( i, myCards.mySelect.list[i] );
		}
		// 手札をソート(数字)
		myCards.cardSort(); //手札をソート
		myCards.viewCards(); // 手札を表示
	}
	//
	// 交換ボタンへのリスナー設定
	koukanBtnElement.addEventListener( 'click', koukanBtnFunc );
	function koukanBtnFunc( ev ) {
		// 選択がなされているか...
		if( myCards.mySelect.trueCount() === 0 ) {
			alert('カードをクリックして交換候補を選択してください。');
		}
		else {
			gameView( CARD_CHANGE ); // 交換処理へ
		}
	}
	//
	// 終了ボタンへのリスナー設定
	finishBtnElement.addEventListener( 'click', finishBtnFunc );
	function finishBtnFunc( ev ) {
		// ゲーム結果の表示
		let result = '';
		for(let i=0; i<myGame.yakuResults.length; i++) {
			//console.log(porkerYakus[i].name,yakuResults[i]);
			result += `${myGame.yakus[i].name} : ${myGame.yakuResults[i]} 回\n`;
		}
		console.log( result );
		//
		// ゲーム終了へ
		gameView( GAME_END );
	}
	//
	// デバグボタン対応
	debugBtnElement.addEventListener( 'click', debugBtnFunc );
	function debugBtnFunc( ev ) {
		// デバグモードがONの時は、交換無限になる。
		debugModeIs = !debugModeIs;
		if( debugModeIs ) {
			ev.target.textContent = 'デバグ[ON]';
		}
		else {
			ev.target.textContent = 'デバグ[OFF]';
		}
		//
		gameView( WAIT );
	}
} // ポーカー以上
//
//
// 素数
const x = 100000;

function sosu1(x) { // 素数探求 その1 定義をそのまま対応
	const sosu = [];
	for(let i=2; i<x; i++) {
		for(let j=2; j<=i; j++) {
			if( i % j === 0 ) {
				if(i === j) {
					sosu.push( i );
				}
				break;
			}
		}
	}
	console.log( `${x}までの素数1:::`,sosu );
}

function sosu2(x) { // 素数探求 その2 チェックは定義をそのまま対応だが、偶数は候補から外す
	const sosu=[2];
	for(let i=1; 2 * i + 1 <=x; i++) {
		let a = 2 * i + 1;
		for(let j=1; 2 * j + 1 <=a; j++) {
			let b = 2 * j + 1;
			if( a % b === 0 ) {
				if(a === b) {
					sosu.push( a );
				}
				break;
			}
		}
	}
	console.log( `${x}までの素数2:::`,sosu );
}

function sosu3(x) {  // 素数探求 その3 偶数を候補から外し、チェック項目を作ったリスト内で対応
	const sosu=[2];
	for(let i=1; 2 * i + 1 <=x; i++) {
		let a = 2 * i + 1; // 素数の候補をはじめから、奇数に制限
		for(let j=0; j < sosu.length; j++) {
			if( a % sosu[j] === 0 ) {
				// sosuの登録リスト内のどれかで割り切れたら、次の候補へ
				break;
			}
			else {
				if( j === sosu.length -1 ) {
					// チェックが最後まで進んだ時(割り切る数がなかった時)...
					sosu.push( a ); // リストに登録
				}
			}
		}
	}
	console.log( `${x}までの素数3:::`,sosu );
}
/**/
function furui(x) {
	const furuiAry = []
	for(let i=0; i<x; i++) { furuiAry[i] = true; }
	furuiAry[0] = false;
	const sosu = [];
	for(let i=1; i<x; i++) {
		if( furuiAry[i] ) {
			sosu.push( i+1 );
			for(let j=1; (i+1)*j<=x; j++) {
				furuiAry[(i+1)*j-1] = false;
			}
		}
	}
	console.log( `${x}までの素数(furui):::`,sosu );
}
/**/
const now = new Date();
let sosuStart = now.getTime();
/**
sosu1(x);
console.log(new Date().getTime() - sosuStart);

sosuStart = new Date().getTime();
sosu2(x);
console.log(new Date().getTime() - sosuStart);
/**/
sosuStart = new Date().getTime();
sosu3(x);
console.log(`素数計算時間::: ${new Date().getTime() - sosuStart} ms`);

sosuStart = new Date().getTime();
furui(x);
console.log(`素数計算時間::: ${new Date().getTime() - sosuStart} ms`);
/**/


//alert( sosu );
//
// 100まで
// 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89, 97.(25こ)
// 1000まで
//2,3,5,7,11,13,17,19,23,29,31,37,41,43,47,53,59,61,67,71,73,79,83,89,97,
//101,103,107,109,113,127,131,137,139,149,151,157,163,167,173,179,181,191,
//193,197,199,211,223,227,229,233,239,241,251,257,263,269,271,277,281,283,
//293,307,311,313,317,331,337,347,349,353,359,367,373,379,383,389,397,401,
//409,419,421,431,433,439,443,449,457,461,463,467,479,487,491,499,503,509,
//521,523,541,547,557,563,569,571,577,587,593,599,601,607,613,617,619,631,
//641,643,647,653,659,661,673,677,683,691,701,709,719,727,733,739,743,751,
//757,761,769,773,787,797,809,811,821,823,827,829,839,853,857,859,863,877,
//881,883,887,907,911,919,929,937,941,947,953,967,971,977,983,991,997.(168こ)
// 10000まで 1229こ
// 100000まで 9592こ 3163ms
//
//
// n進数 変換
if( document.querySelector('form.sinsu') ) {
	//from10進数
	// 表示箇所の登録
	// HTMLへ表示場所
	const from10AnsView = document.querySelector('table#from10');
	// 2進数の数字表示場所
	const ans2sinNumView = from10AnsView.querySelector('tr[class="sinsu2 num"]');
	kokeshi( ans2sinNumView );
	// 2進数のバー表示場所
	const ans2sinBarView = from10AnsView.querySelector('tr[class="sinsu2 bar"]');
	kokeshi( ans2sinBarView );
	// 16進数の数字表示場所
	const ans16sinNumView = from10AnsView.querySelector('tr[class="sinsu16 num"]');
	kokeshi( ans16sinNumView );
	// メインの回答表示場所
	const from10AnsNumView = from10AnsView.querySelector('tr[class="from10Ans"]');
	kokeshi( from10AnsNumView );
	//
	// submitボタンの登録
	const from10BTN = document.querySelector('form.from10 input[type="submit"]');
	from10BTN.addEventListener( 'click', from10BtnFunc, {passive: false} );
	// submitの対応
	function from10BtnFunc( ev ) {
		ev.preventDefault();
		// 表示の初期化
		kokeshi( ans2sinNumView );
		kokeshi( ans2sinBarView );
		kokeshi( ans16sinNumView );
		kokeshi( from10AnsNumView );
		//
		// 10進数からの変換
		const from10Number = Number( document.querySelector('form.from10').inputFrom.value );
		const from10Sinsu = Number( document.querySelector('form.from10').inputSinsu.value );
		//console.log(from10Number, from10Sinsu);
		//
		// 2進数の表示
		const ans2Sin = from10SinToAns( from10Number, 2 );
		for(let i=0; i<ans2Sin.length; i++) {
			ans2sinNumView.insertAdjacentHTML( 'beforeend', 
				`<td class="ans">${ans2Sin[i]}</td>`);
			ans2sinBarView.insertAdjacentHTML( 'beforeend', 
			`<td class="${(ans2Sin[i]===1)?'ON':'OFF'}"></td>`);
		}
		// 16進数表示の為に桁数合わせ
		const keta = ans2Sin.length % 4;
		if(keta !== 0) {
			for(let i=0; i<4-keta; i++) {
				ans2sinNumView.insertAdjacentHTML( 'afterbegin', 
					`<td class="ans"></td>`);
				ans2sinBarView.insertAdjacentHTML( 'afterbegin', 
				`<td class="OFF"></td>`);
			}
		}
		// 16進数の表示
		const ans16Sin = from10SinToAns( from10Number, 16 );
		for(let i=0; i<ans16Sin.length; i++) {
			ans16sinNumView.insertAdjacentHTML( 'beforeend', 
				`<td class="ans" colspan="4">${ans16Sin[i]}</td>`);
		}
		//メインの回答の表示
		// 進数変換
		const ans = from10SinToAns( from10Number, from10Sinsu );
		console.log(`${from10Number}は、${from10Sinsu}進数で、${ans}です。`);
		//
		from10AnsNumView.innerHTML = `<td colspan="${ans2sinBarView.children.length}">${from10Number}は、${from10Sinsu}進数で、<span>【${ans}】</span>です。</td>`;
		from10AnsNumView.querySelector('span').style.color = 'red';
		from10AnsNumView.querySelector('span').style.fontWeight = 'bold';
		from10AnsNumView.querySelector('span').style.fontSize = '16px';
		//
	} /* 以上 From10To */
	//
	// 10進数から○進数へ
	// HTMLへ表示場所
	const to10AnsView = document.querySelector('table#to10');
	// 2進数の数字表示場所
	const ans2sinNumView_to10 = to10AnsView.querySelector('tr[class="sinsu2 num"]');
	kokeshi( ans2sinNumView_to10 );
	// 2進数のバー表示場所
	const ans2sinBarView_to10 = to10AnsView.querySelector('tr[class="sinsu2 bar"]');
	kokeshi( ans2sinBarView_to10 );
	// 16進数の数字表示場所
	const ans16sinNumView_to10 = to10AnsView.querySelector('tr[class="sinsu16 num"]');
	kokeshi( ans16sinNumView_to10 );
	// メインの回答表示場所
	const to10AnsNumView = to10AnsView.querySelector('tr[class="from10Ans"]');
	kokeshi( to10AnsNumView );
	//
	// input,ボタン類の登録
	const to10BTN = document.querySelector('form.to10 input[type="submit"]');
	to10BTN.addEventListener( 'click', to10BtnFunc, {passive: false} );
	// submitの対応
	function to10BtnFunc( ev ) {
		ev.preventDefault();
		// 表示の初期化
		kokeshi( ans2sinNumView_to10 );
		kokeshi( ans2sinBarView_to10 );
		kokeshi( ans16sinNumView_to10 );
		kokeshi( to10AnsNumView );
		// 10進数への変換
		const to10String = String( document.querySelector('form.to10').inputFrom.value );
		const to10Sinsu = Number( document.querySelector('form.to10').inputSinsu.value );
		//console.log(to10String, to10Sinsu);
		//console.log( fromStrTo10Sin(to10String, to10Sinsu) );
		const ans = fromStrTo10Sin(to10String, to10Sinsu); // 10進数への変換
		//
		if( ans === -1 ) {
			// エラーをはいています。
			alert('10進数に変換できません。\n変化前の進数か数の設定に不備があります。\n進数：1～34\n数：桁毎の数は、進数-1で設定してください。');
		}
		else {
			// 10進数の答えを表示
			console.log(`${to10Sinsu}進数の${to10String}は、10進数で、${ans}です。`);
			// 2進数の表示
			const ans2Sin = from10SinToAns( ans, 2 );
			for(let i=0; i<ans2Sin.length; i++) {
				ans2sinNumView_to10.insertAdjacentHTML( 'beforeend', 
					`<td class="ans">${ans2Sin[i]}</td>`);
				ans2sinBarView_to10.insertAdjacentHTML( 'beforeend', 
				`<td class="${(ans2Sin[i]===1)?'ON':'OFF'}"></td>`);
			}
			// 16進数表示の為に桁数合わせ
			const keta = ans2Sin.length % 4;
			if(keta !== 0) {
				for(let i=0; i<4-keta; i++) {
					ans2sinNumView_to10.insertAdjacentHTML( 'afterbegin', 
						`<td class="ans"></td>`);
					ans2sinBarView_to10.insertAdjacentHTML( 'afterbegin', 
					`<td class="OFF"></td>`);
				}
			}
			// 16進数の表示
			const ans16Sin = from10SinToAns( ans, 16 );
			for(let i=0; i<ans16Sin.length; i++) {
				ans16sinNumView_to10.insertAdjacentHTML( 'beforeend', 
					`<td class="ans" colspan="4">${ans16Sin[i]}</td>`);
			}
			//メインの回答の表示
			// 10進数 表示
			to10AnsNumView.innerHTML = `<td colspan="${ans2sinBarView_to10.children.length}">${to10Sinsu}進数の${to10String}は、10進数で、<span>【${ans}】</span>です。</td>`;
			to10AnsNumView.querySelector('span').style.color = 'red';
			to10AnsNumView.querySelector('span').style.fontWeight = 'bold';
			to10AnsNumView.querySelector('span').style.fontSize = '16px';
			//
		}
	}
	//
	// ○進数から10進数へ変換関数
	function fromStrTo10Sin( str, sinsu ) {
		//console.log('str::',str,',sinsu::',sinsu);
		let sin10Ex = 0;
		const temp = [];
		for(let i=0; i<str.length; i++) {
			// 1文字ずつ拾って数値化する
			//console.log(str[i],'::::',henkanStrToNum(str[i]));
			let checkNum = henkanStrToNum( str[i] );
			if ( checkNum < sinsu ) {
				temp.push( checkNum );
			}
			else {
				temp.push( -1 ); // ○進数の範囲外なら-1を格納
			}
		}
		//console.log(temp);
		for(let i=0; i<temp.length; i++) {
			if( temp[i] === -1 ) {
				sin10Ex = -1; // 範囲外になっていればエラーとして-1
				break;
			}
			else{
				let tempNum = temp[i];
				for(let j=i; j<temp.length-1; j++) {
					tempNum *= sinsu;
				}
				sin10Ex += tempNum;
			}
		}
		return sin10Ex;
	}
	//
	function kokeshi( element ) {
		//console.log(element);
		// 要素を空にする
		while( element.children.length > 0 ) {
			element.removeChild( element.children[0] );
		}
	}
	// 10進数から○進数へ変換関数
	function from10SinToAns( num, sinsu ) {
		const nSinsuEx = [];
		//console.log('num::',num,',sinsu::',sinsu);
		while( num >= sinsu ) {
			//console.log('check::',num%sinsu);
			nSinsuEx.push( henkanOver9( num % sinsu ) );
			num = Math.floor( num / sinsu );
			//console.log('next::',num);
		}
		nSinsuEx.push( henkanOver9( num ) );
		//
		return nSinsuEx.reverse();
	}
	//
	// 9以上をアルファベットに変換
	function henkanOver9( num ) {
		//console.log('henkanOver9:::',num);
		const eisu = ['a', 'b', 'c', 'd', 'e',
					'f', 'g', 'h', 'i', 'j',
					'k', 'l', 'm', 'n', 'o',
					'p', 'r', 's', 't', 'u',
					'v', 'w', 'x', 'y', 'z'];
		//
		return ( num - 9 > 0 ) ? eisu[(num - 9) - 1] : num;
	}/**/
	// 文字(数字,アルファベット)を数値に変換
	function henkanStrToNum( str ) {
		const eisu = ['a', 'b', 'c', 'd', 'e',
					'f', 'g', 'h', 'i', 'j',
					'k', 'l', 'm', 'n', 'o',
					'p', 'r', 's', 't', 'u',
					'v', 'w', 'x', 'y', 'z'];
		//
		return ( Number( str ) <= 9 ) ? Number(str) : 10 + eisu.indexOf(str);
	}
	//
	// 初期表示の処理
	function startSinsu() {
		//from10BTN.dispatchEvent(new Event('click')); // あとで表示方法を考える
		//to10BTN.dispatchEvent(new Event('click'));
		from10BtnFunc(new Event('dummy'));
		to10BtnFunc(new Event('dummy'));
	}
	startSinsu();
}