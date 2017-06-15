//alert(" Welcome to Packt ");
function subtraction (a, b)
{	
	var c;
	c = a - b;
	return c;
}

function getRandomIntInclusive(min, max) {
  min = Math.ceil(min);
  max = Math.floor(max);
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

function d6 ()
{
	var value;
	value = getRandomIntInclusive(1,6);
	//document.write("value = " + value + "   ");
	return (value);
}

function getDiceQuantity ()
{
	var numberOfDice = 0;
	numberOfDice = prompt ("How many dice per statistic?", "");
	return (numberOfDice);
}

function getCharacterName ()
{
	var name;
	name = prompt ("What is this character's name?", "");
	return (name);
}

function rollDice (numberOfDice)
{
	//alert("numberOfDice = " + numberOfDice);
	var total = 0;
	var least = 7;
	for (var i=0;i<numberOfDice;i++)
	{	
		var temp = d6();
				if (temp < least)
			{least = temp}
		total = total + temp;
	}
	//alert("Total = " + total + ", Least = " + least);
	total = total - least;
	//alert ("The total rolled is " + total);
	return (total);
}

function rollCharacter (name)
{
	var stats = new Array();	
	var diceNum = 4;

	//document.write("In RollCharacter, diceNum = " + diceNum);
	//alert("In RollCharacter, diceNum = " + diceNum);
	for (var i=0;i<6;i++)
	{
		var temp = rollDice(diceNum);
		//alert ("In rollCharacter, i=" + i + ", temp = " + temp);
		stats[i]=temp;
		//alert ("In rollCharacter, i=" + i + ", stat = " + stats[i]);
	}

	var output = name + "'s stats rolled are as follows: ";

	for (i=0;i<6;i++){
			output = output + stats[i];
			if (i<5) {
				output = output + ", ";
			}
		}
	return (output);
}

	//var diceroll = d6();
	//document.write("<p>diceroll = " + diceroll + "</p>");
	//document.write("<p>After subtracting " + diceroll + " from 10, you get " + subtraction(10, diceroll) + "</p>");
	//var numOfDice = getDiceQuantity();
	//rollDice(numOfDice);

	//var name = getCharacterName();
	//rollCharacter('Bob');